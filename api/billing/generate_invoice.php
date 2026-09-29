<?php
/**
 * API: Billing - Generate Invoices (Single & Bulk) with Multi-Tenant Scoping
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/WhatsAppMetaService.php';
require_once __DIR__ . '/../../services/DLTNotificationService.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();
$orgId = TenantContext::getOrgId();
$activeBldId = TenantContext::getBuildingId();

if (!$orgId) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: No organization context'], 401);
}

if ($method === 'GET') {
    $tenantId = $_GET['tenant_id'] ?? null;
    $status = $_GET['status'] ?? null;
    $month = $_GET['month'] ?? null;

    $sql = "
        SELECT i.*, u.name as tenant_name, u.phone as tenant_phone,
               r.room_number, b.bed_number, bld.building_name
        FROM invoices i
        JOIN users u ON i.tenant_id = u.id
        JOIN rooms r ON i.room_id = r.id
        JOIN beds b ON i.bed_id = b.id
        JOIN buildings bld ON i.building_id = bld.id
        WHERE i.organization_id = ?
    ";
    $params = [$orgId];

    if ($activeBldId) {
        $sql .= " AND i.building_id = ?";
        $params[] = $activeBldId;
    }
    if (!empty($tenantId)) {
        $sql .= " AND i.tenant_id = ?";
        $params[] = (int)$tenantId;
    }
    if (!empty($status)) {
        $sql .= " AND i.status = ?";
        $params[] = $status;
    }
    if (!empty($month)) {
        $sql .= " AND i.billing_month = ?";
        $params[] = $month;
    }

    $sql .= " ORDER BY i.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $invoices = $stmt->fetchAll();

    sendJSON(['success' => true, 'data' => $invoices]);
}

$admin = requireAuth('admin');

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? 'single';
    $billingMonth = trim($input['billing_month'] ?? date('Y-m'));
    $dueDate = trim($input['due_date'] ?? date('Y-m-05', strtotime($billingMonth . '-01')));
    $bldId = !empty($input['building_id']) ? (int)$input['building_id'] : $activeBldId;

    if ($action === 'bulk') {
        $res = BillingEngine::generateMonthlyInvoices($orgId, $bldId, $billingMonth);
        sendJSON($res);
    } else {
        // Single invoice creation
        $tenantId = (int)($input['tenant_id'] ?? 0);
        $rent = (float)($input['rent_amount'] ?? 0);
        $ebAmount = (float)($input['eb_amount'] ?? 0);
        $ebUnits = (float)($input['eb_units'] ?? 0);
        $maint = (float)($input['maintenance_amount'] ?? 300);
        $food = (float)($input['food_amount'] ?? 0);
        $laundry = (float)($input['laundry_amount'] ?? 0);
        $other = (float)($input['other_amount'] ?? 0);
        $lateFee = (float)($input['late_fee'] ?? 0);
        $discount = (float)($input['discount_amount'] ?? 0);
        $notes = trim($input['notes'] ?? '');

        if ($tenantId <= 0 || $rent <= 0) {
            sendJSON(['success' => false, 'message' => 'Valid Resident and Rent amount are required.'], 400);
        }

        // Get tenant's active booking & building (Strictly scoped to current organization)
        $stmtBooking = $db->prepare("
            SELECT tb.*, bld.invoice_prefix 
            FROM tenant_bookings tb
            JOIN buildings bld ON tb.building_id = bld.id
            WHERE tb.tenant_id = ? AND tb.organization_id = ? AND tb.status = 'active' 
            LIMIT 1
        ");
        $stmtBooking->execute([$tenantId, $orgId]);
        $booking = $stmtBooking->fetch();

        if (!$booking) {
            sendJSON(['success' => false, 'message' => 'No active bed booking found for this resident.'], 404);
        }

        $calculated = BillingEngine::calculateInvoice([
            'rent_amount' => $rent,
            'eb_amount' => $ebAmount,
            'eb_units' => $ebUnits,
            'maintenance_amount' => $maint,
            'food_amount' => $food,
            'laundry_amount' => $laundry,
            'other_amount' => $other,
            'late_fee' => $lateFee,
            'discount_amount' => $discount
        ]);

        $prefix = $booking['invoice_prefix'] ?: 'INV';
        $invoiceNo = sprintf("%s-%s-%03d", $prefix, str_replace('-', '', $billingMonth), random_int(100, 999));

        $stmtIns = $db->prepare("
            INSERT INTO invoices (
                organization_id, building_id, invoice_no, tenant_id, room_id, bed_id,
                billing_month, rent_amount, eb_units, eb_amount, maintenance_amount,
                food_amount, laundry_amount, other_amount, late_fee, discount_amount,
                subtotal_amount, total_amount, paid_amount, due_date, status, notes
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, 0.00, ?, 'pending', ?
            )
        ");
        $stmtIns->execute([
            $orgId, $booking['building_id'], $invoiceNo, $tenantId, $booking['room_id'], $booking['bed_id'],
            $billingMonth, $calculated['rent_amount'], $calculated['eb_units'], $calculated['eb_amount'], $calculated['maintenance_amount'],
            $calculated['food_amount'], $calculated['laundry_amount'], $calculated['other_amount'], $calculated['late_fee'], $calculated['discount_amount'],
            $calculated['subtotal_amount'], $calculated['total_amount'], $dueDate, $notes
        ]);
        $invId = (int)$db->lastInsertId();

        // Audit log
        AuditService::log('invoice_created', 'invoice', $invId, null, $calculated, $orgId, (int)$booking['building_id']);

        // Dispatch notifications
        $stmtU = $db->prepare("SELECT name, phone FROM users WHERE id = ?");
        $stmtU->execute([$tenantId]);
        $u = $stmtU->fetch();
        if ($u && !empty($u['phone'])) {
            WhatsAppMetaService::sendInvoiceCreated($u['phone'], $u['name'], $invoiceNo, $calculated['total_amount'], $dueDate);
        }

        sendJSON([
            'success' => true,
            'message' => "Invoice #{$invoiceNo} created successfully!",
            'invoice_id' => $invId,
            'invoice_no' => $invoiceNo,
            'total_amount' => $calculated['total_amount']
        ]);
    }
}
