<?php
/**
 * StayFlow PG SaaS — Unified Web & CLI Cron Task Runner
 * 
 * Usage via Web (e.g. cron-job.org or external scheduler):
 * https://yourdomain.com/cron.php?key=YOUR_CRON_KEY&task=all
 * 
 * Usage via CLI:
 * php cron.php --key=YOUR_CRON_KEY --task=all
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

$isCli = (php_sapi_name() === 'cli');
$keyProvided = $_GET['key'] ?? $_POST['key'] ?? '';

if ($isCli) {
    global $argv;
    foreach ($argv ?? [] as $arg) {
        if (str_starts_with($arg, '--key=')) {
            $keyProvided = substr($arg, 6);
        }
    }
}

// Verify CRON_KEY or super-admin session
$expectedKey = defined('CRON_KEY') ? CRON_KEY : 'STAYFLOW_CRON_SECURE_2026';
$isAdmin = isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'super_admin']);

if (!$isCli && !$isAdmin && !hash_equals($expectedKey, $keyProvided)) {
    http_response_code(403);
    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        sendJSON(['success' => false, 'message' => 'Unauthorized cron access.'], 403);
    }
    die('Forbidden: Invalid cron key.');
}

$task = $_GET['task'] ?? $_POST['task'] ?? 'all';
$db = getDB();
$results = [];

// 1. Task: Overdue Gatepass Check
if ($task === 'all' || $task === 'gatepass') {
    try {
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            UPDATE gatepasses 
            SET status = 'OVERDUE' 
            WHERE status = 'CURRENTLY OUT' 
              AND expected_in_time < ? 
              AND actual_in_time IS NULL
        ");
        $stmt->execute([$now]);
        $overdueCount = $stmt->rowCount();
        $results['gatepass_overdue_updated'] = $overdueCount;
    } catch (\Throwable $e) {
        $results['gatepass_error'] = $e->getMessage();
    }
}

// 2. Task: Invoices Auto-Generation (1st of month)
if ($task === 'all' || $task === 'invoices') {
    try {
        $targetMonth = date('Y-m');
        $dueDate = date('Y-m-05');
        $maintenance = 300.00;

        $stmt = $db->query("
            SELECT tb.*, u.name, u.phone, u.email, r.room_number, b.bed_number
            FROM tenant_bookings tb
            JOIN users u ON tb.tenant_id = u.id
            JOIN rooms r ON tb.room_id = r.id
            JOIN beds b ON tb.bed_id = b.id
            WHERE tb.status = 'active'
        ");
        $tenants = $stmt->fetchAll();
        $created = 0;

        foreach ($tenants as $t) {
            $stmtCheck = $db->prepare("SELECT id FROM invoices WHERE tenant_id = ? AND billing_month = ?");
            $stmtCheck->execute([$t['tenant_id'], $targetMonth]);
            if ($stmtCheck->rowCount() > 0) continue;

            $rent = (float)$t['monthly_rent'];
            $total = $rent + $maintenance;
            $invoiceNo = 'INV-' . date('Ym') . '-' . str_pad((string)$t['tenant_id'], 4, '0', STR_PAD_LEFT);

            $stmtIns = $db->prepare("
                INSERT INTO invoices (
                    invoice_no, organization_id, property_id, tenant_id, booking_id, billing_month,
                    rent_amount, maintenance_amount, electricity_amount, other_charges,
                    total_amount, paid_amount, due_date, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, 0.00, ?, 0.00, ?, 'pending', NOW())
            ");
            $stmtIns->execute([
                $invoiceNo,
                $t['organization_id'] ?? 1,
                $t['building_id'] ?? 1,
                $t['tenant_id'],
                $t['id'],
                $targetMonth,
                $rent,
                $maintenance,
                $total,
                $dueDate
            ]);
            $created++;
        }
        $results['invoices_created'] = $created;
    } catch (\Throwable $e) {
        $results['invoices_error'] = $e->getMessage();
    }
}

// 3. Task: Prune Expired Tokens & Temporary OTPs
if ($task === 'all' || $task === 'cleanup') {
    try {
        $stmtTokens = $db->query("DELETE FROM refresh_tokens WHERE expires_at < NOW()");
        $results['expired_tokens_pruned'] = $stmtTokens->rowCount();

        $stmtOtps = $db->query("DELETE FROM email_otps WHERE expires_at < NOW()");
        $results['expired_otps_pruned'] = $stmtOtps->rowCount();
    } catch (\Throwable $e) {
        $results['cleanup_error'] = $e->getMessage();
    }
}

// Output Results
if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json') || isset($_GET['json'])) {
    sendJSON([
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'results' => $results
    ]);
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== StayFlow Cron Execution Summary (" . date('Y-m-d H:i:s') . ") ===\n";
    foreach ($results as $k => $v) {
        echo " - {$k}: {$v}\n";
    }
    echo "Done.\n";
}
