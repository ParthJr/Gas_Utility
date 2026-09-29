<?php
/**
 * API: WhatsApp Communication & Context Data Provider (wa.me Redirect System)
 * Strictly Scoped to Tenant Organization. Requires NO external WhatsApp API.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/WhatsAppHelper.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

$db = getDB();
$orgId = TenantContext::getOrgId();
$activeBldId = TenantContext::getBuildingId();

if (!$orgId) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: No organization context'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];

// Fetch Org Company Name
$stmtOrg = $db->prepare("SELECT company_name FROM organizations WHERE id = ?");
$stmtOrg->execute([$orgId]);
$orgName = $stmtOrg->fetchColumn() ?: (defined('APP_NAME') ? APP_NAME : 'PG Management');

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'residents';

    // 1. Active Residents List
    if ($action === 'residents') {
        $sql = "
            SELECT u.id, u.name, u.phone, u.email,
                   tb.id as active_booking_id, tb.monthly_rent,
                   b.bed_number, r.room_number, r.room_type, bld.building_name,
                   (SELECT COUNT(*) FROM invoices i WHERE i.tenant_id = u.id AND i.status IN ('pending', 'overdue') AND i.organization_id = ?) as pending_invoices_count,
                   (SELECT COALESCE(SUM(total_amount - paid_amount), 0) FROM invoices i WHERE i.tenant_id = u.id AND i.status IN ('pending', 'overdue') AND i.organization_id = ?) as total_due_amount
            FROM users u
            LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.organization_id = u.organization_id AND tb.status = 'active'
            LEFT JOIN beds b ON tb.bed_id = b.id
            LEFT JOIN rooms r ON tb.room_id = r.id
            LEFT JOIN buildings bld ON u.building_id = bld.id
            WHERE u.organization_id = ? AND u.role = 'tenant' AND u.status = 'active'
        ";
        $params = [$orgId, $orgId, $orgId];
        if ($activeBldId) {
            $sql .= " AND (u.building_id = ? OR u.building_id IS NULL)";
            $params[] = $activeBldId;
        }
        $sql .= " ORDER BY u.name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'org_name' => $orgName, 'data' => $residents]);
    }

    // 2. Pending & Overdue Invoices (Payment Reminders)
    elseif ($action === 'pending_invoices') {
        $sql = "
            SELECT i.id, i.invoice_no, i.billing_month, i.rent_amount, i.total_amount, i.paid_amount,
                   (i.total_amount - i.paid_amount) as balance_due, i.due_date, i.status,
                   u.id as tenant_id, u.name as tenant_name, u.phone as tenant_phone,
                   COALESCE(r.room_number, r_act.room_number, 'N/A') as room_number,
                   COALESCE(b.bed_number, b_act.bed_number, 'N/A') as bed_number
            FROM invoices i
            JOIN users u ON i.tenant_id = u.id
            LEFT JOIN rooms r ON i.room_id = r.id
            LEFT JOIN beds b ON i.bed_id = b.id
            LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.organization_id = u.organization_id AND tb.status = 'active'
            LEFT JOIN rooms r_act ON tb.room_id = r_act.id
            LEFT JOIN beds b_act ON tb.bed_id = b_act.id
            WHERE i.organization_id = ? AND i.status IN ('pending', 'overdue')
        ";
        $params = [$orgId];
        if ($activeBldId) {
            $sql .= " AND i.building_id = ?";
            $params[] = $activeBldId;
        }
        $sql .= " ORDER BY i.due_date ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'org_name' => $orgName, 'data' => $invoices]);
    }

    // 2.1 Dynamic Single Source of Truth Invoice Details with Verification
    elseif ($action === 'invoice_details') {
        $invoiceId = (int)($_GET['invoice_id'] ?? 0);
        if ($invoiceId <= 0) {
            sendJSON(['success' => false, 'mismatch' => true, 'message' => 'Valid Invoice ID required.'], 400);
        }

        $stmtInv = $db->prepare("
            SELECT i.id, i.invoice_no, i.organization_id, i.building_id, i.tenant_id,
                   i.room_id, i.bed_id, i.billing_month, i.rent_amount, i.total_amount, i.paid_amount,
                   (i.total_amount - i.paid_amount) as balance_due, i.due_date, i.status,
                   u.id as user_id, u.organization_id as user_org_id, u.name as tenant_name, u.phone as tenant_phone, u.status as user_status,
                   r.id as room_table_id, r.room_number, r.room_type, r.organization_id as room_org_id,
                   b.id as bed_table_id, b.bed_number, b.organization_id as bed_org_id,
                   tb.room_id as active_booking_room_id, tb.bed_id as active_booking_bed_id,
                   r_act.room_number as active_room_number, b_act.bed_number as active_bed_number
            FROM invoices i
            LEFT JOIN users u ON i.tenant_id = u.id
            LEFT JOIN rooms r ON i.room_id = r.id
            LEFT JOIN beds b ON i.bed_id = b.id
            LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.organization_id = u.organization_id AND tb.status = 'active'
            LEFT JOIN rooms r_act ON tb.room_id = r_act.id
            LEFT JOIN beds b_act ON tb.bed_id = b_act.id
            WHERE i.id = ? AND i.organization_id = ?
        ");
        $stmtInv->execute([$invoiceId, $orgId]);
        $inv = $stmtInv->fetch(PDO::FETCH_ASSOC);

        // --- STRICT INTEGRITY & MISMATCH VERIFICATION ---
        if (!$inv) {
            sendJSON([
                'success' => false,
                'mismatch' => true,
                'message' => 'Invoice and resident allocation data do not match'
            ], 404);
        }

        // Verify Resident Exists and belongs to Organization
        if (empty($inv['user_id']) || (int)$inv['user_org_id'] !== $orgId) {
            sendJSON([
                'success' => false,
                'mismatch' => true,
                'message' => 'Invoice and resident allocation data do not match'
            ], 400);
        }

        // Verify Room and Bed ownership if set on invoice
        if (!empty($inv['room_org_id']) && (int)$inv['room_org_id'] !== $orgId) {
            sendJSON([
                'success' => false,
                'mismatch' => true,
                'message' => 'Invoice and resident allocation data do not match'
            ], 400);
        }
        if (!empty($inv['bed_org_id']) && (int)$inv['bed_org_id'] !== $orgId) {
            sendJSON([
                'success' => false,
                'mismatch' => true,
                'message' => 'Invoice and resident allocation data do not match'
            ], 400);
        }

        // Resolve room and bed number dynamically from invoice, falling back to current active allocation
        $resolvedRoomNumber = !empty($inv['room_number']) ? $inv['room_number'] : ($inv['active_room_number'] ?? 'N/A');
        $resolvedBedNumber = !empty($inv['bed_number']) ? $inv['bed_number'] : ($inv['active_bed_number'] ?? 'N/A');

        $balanceDue = (float)$inv['balance_due'];
        if ($balanceDue <= 0 && $inv['status'] === 'paid') {
            $balanceDue = 0.00;
        }

        sendJSON([
            'success' => true,
            'org_name' => $orgName,
            'data' => [
                'invoice_id' => (int)$inv['id'],
                'invoice_no' => $inv['invoice_no'],
                'tenant_id' => (int)$inv['tenant_id'],
                'tenant_name' => $inv['tenant_name'],
                'tenant_phone' => $inv['tenant_phone'],
                'room_number' => $resolvedRoomNumber,
                'bed_number' => $resolvedBedNumber,
                'rent_amount' => (float)$inv['rent_amount'],
                'total_amount' => (float)$inv['total_amount'],
                'paid_amount' => (float)$inv['paid_amount'],
                'balance_due' => $balanceDue,
                'due_date' => $inv['due_date'],
                'billing_month' => $inv['billing_month'],
                'status' => $inv['status']
            ]
        ]);
    }

    // 3. Recent Issued Invoices
    elseif ($action === 'recent_invoices') {
        $sql = "
            SELECT i.id, i.invoice_no, i.billing_month, i.total_amount, i.due_date, i.status, i.created_at,
                   u.id as tenant_id, u.name as tenant_name, u.phone as tenant_phone,
                   COALESCE(r.room_number, r_act.room_number, 'N/A') as room_number,
                   COALESCE(b.bed_number, b_act.bed_number, 'N/A') as bed_number
            FROM invoices i
            JOIN users u ON i.tenant_id = u.id
            LEFT JOIN rooms r ON i.room_id = r.id
            LEFT JOIN beds b ON i.bed_id = b.id
            LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.organization_id = u.organization_id AND tb.status = 'active'
            LEFT JOIN rooms r_act ON tb.room_id = r_act.id
            LEFT JOIN beds b_act ON tb.bed_id = b_act.id
            WHERE i.organization_id = ?
        ";
        $params = [$orgId];
        if ($activeBldId) {
            $sql .= " AND i.building_id = ?";
            $params[] = $activeBldId;
        }
        $sql .= " ORDER BY i.created_at DESC LIMIT 30";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'org_name' => $orgName, 'data' => $invoices]);
    }

    // 4. Approved Gate Passes
    elseif ($action === 'approved_gatepasses') {
        $sql = "
            SELECT gp.id, gp.pass_code, gp.out_datetime, gp.expected_in_datetime, gp.reason, gp.status,
                   u.id as tenant_id, u.name as tenant_name, u.phone as tenant_phone,
                   r.room_number, b.bed_number
            FROM gatepasses gp
            JOIN users u ON gp.tenant_id = u.id
            LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.status = 'active'
            LEFT JOIN rooms r ON tb.room_id = r.id
            LEFT JOIN beds b ON tb.bed_id = b.id
            WHERE gp.organization_id = ? AND gp.status = 'approved'
        ";
        $params = [$orgId];
        if ($activeBldId) {
            $sql .= " AND gp.building_id = ?";
            $params[] = $activeBldId;
        }
        $sql .= " ORDER BY gp.created_at DESC LIMIT 30";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $passes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'org_name' => $orgName, 'data' => $passes]);
    }

    // 5. Resolved Complaints
    elseif ($action === 'resolved_complaints') {
        $sql = "
            SELECT c.id, c.title, c.description, c.category, c.status, c.created_at,
                   u.id as tenant_id, u.name as tenant_name, u.phone as tenant_phone,
                   r.room_number
            FROM complaints c
            JOIN users u ON c.tenant_id = u.id
            LEFT JOIN rooms r ON c.room_id = r.id
            WHERE c.organization_id = ? AND c.status IN ('resolved', 'closed')
        ";
        $params = [$orgId];
        if ($activeBldId) {
            $sql .= " AND c.building_id = ?";
            $params[] = $activeBldId;
        }
        $sql .= " ORDER BY c.created_at DESC LIMIT 30";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'org_name' => $orgName, 'data' => $complaints]);
    }

    // 6. Recent Communication Activity Logs
    elseif ($action === 'logs') {
        $stmtLogs = $db->prepare("
            SELECT * FROM notifications 
            WHERE tenant_organization_id = ? 
            ORDER BY created_at DESC LIMIT 20
        ");
        $stmtLogs->execute([$orgId]);
        $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'data' => $logs]);
    }

    // 7. Default Templates
    elseif ($action === 'templates') {
        sendJSON(['success' => true, 'org_name' => $orgName, 'data' => WhatsAppHelper::getDefaultTemplates()]);
    }
}

$admin = requireAuth('admin');

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';

    // ==========================================
    // ACTION: LOG WHATSAPP REDIRECT ACTIVITY
    // Records status strictly as OPENED_IN_WHATSAPP
    // ==========================================
    if ($action === 'log_activity') {
        $type = trim($input['type'] ?? 'WHATSAPP_INSTANT');
        $title = trim($input['title'] ?? 'WhatsApp Message');
        $message = trim($input['message'] ?? '');
        $recipientName = trim($input['recipient_name'] ?? 'Resident');
        $recipientPhone = WhatsAppHelper::formatPhoneNumber($input['recipient_phone'] ?? '');

        if (empty($message)) {
            sendJSON(['success' => false, 'message' => 'Message content is required.'], 400);
        }

        $metadata = [
            'status' => 'OPENED_IN_WHATSAPP',
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'channel' => 'whatsapp_web_redirect',
            'logged_at' => date('Y-m-d H:i:s')
        ];

        // Insert into notifications
        $stmtIns = $db->prepare("
            INSERT INTO notifications (tenant_organization_id, type, title, message, priority, metadata, created_by, created_at)
            VALUES (?, ?, ?, ?, 'high', ?, ?, NOW())
        ");
        $stmtIns->execute([
            $orgId,
            $type,
            $title,
            $message,
            json_encode($metadata),
            (int)($_SESSION['user_id'] ?? 1)
        ]);
        $notifId = (int)$db->lastInsertId();

        AuditService::log('whatsapp_redirect_opened', 'communication', $notifId, null, [
            'type' => $type,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'status' => 'OPENED_IN_WHATSAPP'
        ], $orgId, $activeBldId);

        sendJSON([
            'success' => true,
            'status' => 'OPENED_IN_WHATSAPP',
            'message' => 'WhatsApp opened — please press Send in WhatsApp.'
        ]);
    }
}
