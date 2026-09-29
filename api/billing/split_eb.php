<?php
/**
 * API: Billing - Electricity Bill Sub-meter Split Engine
 */

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

$orgId = TenantContext::getOrgId();
$activeBldId = TenantContext::getBuildingId();

if ($method === 'GET') {
    if (!$orgId && !TenantContext::isSuperAdmin()) {
        sendJSON(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    // Get EB history for a room or all rooms for a given month
    $roomId = $_GET['room_id'] ?? null;
    $month = $_GET['month'] ?? date('Y-m');

    $sql = "
        SELECT eb.*, r.room_number, r.floor, r.room_type, r.ac_type
        FROM eb_readings eb
        JOIN rooms r ON eb.room_id = r.id
        WHERE eb.organization_id = ?
    ";
    $params = [$orgId];

    if ($activeBldId) {
        $sql .= " AND eb.building_id = ?";
        $params[] = $activeBldId;
    }
    if (!empty($roomId)) {
        $sql .= " AND eb.room_id = ?";
        $params[] = (int)$roomId;
    }
    if (!empty($month)) {
        $sql .= " AND eb.billing_month = ?";
        $params[] = $month;
    }

    $sql .= " ORDER BY eb.billing_month DESC, r.room_number ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $readings = $stmt->fetchAll();

    sendJSON(['success' => true, 'data' => $readings]);
}

$admin = requireAuth('admin');

if ($method === 'POST') {
    if (!$orgId) {
        sendJSON(['success' => false, 'message' => 'Unauthorized organization context'], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $roomId = (int)($input['room_id'] ?? 0);
    $billingMonth = trim($input['billing_month'] ?? date('Y-m'));
    $currentReading = (float)($input['current_reading'] ?? 0);
    $previousReading = (float)($input['previous_reading'] ?? -1);
    $ratePerUnit = (float)($input['rate_per_unit'] ?? DEFAULT_ELECTRICITY_RATE_PER_UNIT);

    if ($roomId <= 0 || $currentReading <= 0) {
        sendJSON(['success' => false, 'message' => 'Room ID and valid Current Reading are required.'], 400);
    }

    // Verify room belongs to current organization
    $stmtRoom = $db->prepare("SELECT id, building_id FROM rooms WHERE id = ? AND organization_id = ?");
    $stmtRoom->execute([$roomId, $orgId]);
    $roomRow = $stmtRoom->fetch();
    if (!$roomRow) {
        sendJSON(['success' => false, 'message' => 'Room not found or unauthorized.'], 404);
    }
    $bldId = (int)$roomRow['building_id'];

    // If previous reading not provided, look up last recorded reading for this room
    if ($previousReading < 0) {
        $stmtLast = $db->prepare("
            SELECT current_reading 
            FROM eb_readings 
            WHERE room_id = ? AND organization_id = ? AND billing_month < ? 
            ORDER BY billing_month DESC LIMIT 1
        ");
        $stmtLast->execute([$roomId, $orgId, $billingMonth]);
        $previousReading = (float)($stmtLast->fetchColumn() ?: 0.00);
    }

    if ($currentReading < $previousReading) {
        sendJSON(['success' => false, 'message' => "Current reading ({$currentReading}) cannot be less than previous reading ({$previousReading})."], 400);
    }

    $unitsConsumed = round($currentReading - $previousReading, 2);
    $totalAmount = round($unitsConsumed * $ratePerUnit, 2);

    // Count active occupants in this room
    $stmtOccupants = $db->prepare("
        SELECT b.current_tenant_id 
        FROM beds b 
        WHERE b.room_id = ? AND b.organization_id = ? AND b.status = 'occupied' AND b.current_tenant_id IS NOT NULL
    ");
    $stmtOccupants->execute([$roomId, $orgId]);
    $occupantIds = $stmtOccupants->fetchAll(PDO::FETCH_COLUMN);
    $occupantCount = count($occupantIds);

    if ($occupantCount > 0) {
        $splitAmountPerTenant = round($totalAmount / $occupantCount, 2);
    } else {
        $occupantCount = 0;
        $splitAmountPerTenant = 0.00;
    }
    $readingDate = date('Y-m-d');

    // Save or update EB reading
    $stmtCheck = $db->prepare("SELECT id FROM eb_readings WHERE room_id = ? AND organization_id = ? AND billing_month = ?");
    $stmtCheck->execute([$roomId, $orgId, $billingMonth]);
    $existingId = $stmtCheck->fetchColumn();

    if ($existingId) {
        $stmtUp = $db->prepare("
            UPDATE eb_readings 
            SET previous_reading = ?, current_reading = ?, units_consumed = ?, rate_per_unit = ?, 
                total_amount = ?, occupant_count = ?, split_amount_per_tenant = ?, reading_date = ?, recorded_by = ?
            WHERE id = ? AND organization_id = ?
        ");
        $stmtUp->execute([$previousReading, $currentReading, $unitsConsumed, $ratePerUnit, $totalAmount, $occupantCount, $splitAmountPerTenant, $readingDate, $admin['id'], $existingId, $orgId]);
        $ebReadingId = $existingId;
    } else {
        $stmtIns = $db->prepare("
            INSERT INTO eb_readings 
            (organization_id, building_id, room_id, billing_month, previous_reading, current_reading, units_consumed, rate_per_unit, total_amount, occupant_count, split_amount_per_tenant, reading_date, recorded_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtIns->execute([$orgId, $bldId, $roomId, $billingMonth, $previousReading, $currentReading, $unitsConsumed, $ratePerUnit, $totalAmount, $occupantCount, $splitAmountPerTenant, $readingDate, $admin['id']]);
        $ebReadingId = $db->lastInsertId();
    }

    // Auto-update pending invoices for these tenants for this billing month
    foreach ($occupantIds as $tId) {
        $stmtInvCheck = $db->prepare("SELECT id, rent_amount, maintenance_amount, late_fee, discount_amount FROM invoices WHERE tenant_id = ? AND organization_id = ? AND billing_month = ?");
        $stmtInvCheck->execute([$tId, $orgId, $billingMonth]);
        $inv = $stmtInvCheck->fetch();
        if ($inv) {
            $newTotal = $inv['rent_amount'] + $splitAmountPerTenant + $inv['maintenance_amount'] + $inv['late_fee'] - $inv['discount_amount'];
            $stmtUpInv = $db->prepare("UPDATE invoices SET eb_units = ?, eb_amount = ?, total_amount = ? WHERE id = ? AND organization_id = ?");
            $stmtUpInv->execute([round($unitsConsumed / $occupantCount, 2), $splitAmountPerTenant, $newTotal, $inv['id'], $orgId]);
        }
    }

    sendJSON([
        'success' => true,
        'message' => "Electricity reading saved! {$unitsConsumed} units = \u20b9{$totalAmount} split among {$occupantCount} occupants (\u20b9{$splitAmountPerTenant} each).",
        'data' => [
            'id' => $ebReadingId,
            'room_id' => $roomId,
            'billing_month' => $billingMonth,
            'units_consumed' => $unitsConsumed,
            'total_amount' => $totalAmount,
            'occupant_count' => $occupantCount,
            'split_amount_per_tenant' => $splitAmountPerTenant
        ]
    ]);
}
