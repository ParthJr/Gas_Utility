<?php
/**
 * API: Gate Pass - Tenant Request Pass & Pass Listing
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/EntitlementService.php';
require_once __DIR__ . '/../../services/WhatsAppMetaService.php';

header('Content-Type: application/json; charset=utf-8');

$user = requireAuth();
$orgId = TenantContext::getOrgId();
EntitlementService::requireAnyFeature($orgId, ['gate_pass', 'qr_gate_pass', 'visitor_management', 'check_in_out']);

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $tenantId = ($user['role'] === 'tenant') ? $user['id'] : ($_GET['tenant_id'] ?? null);
    $status = $_GET['status'] ?? null;
    $activeBldId = TenantContext::getBuildingId();

    $sql = "
        SELECT gp.*, u.name as tenant_name, u.phone as tenant_phone, 
               u.parent_name, u.parent_phone, r.room_number, b.bed_number
        FROM gatepasses gp
        JOIN users u ON gp.tenant_id = u.id
        LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.status = 'active'
        LEFT JOIN rooms r ON tb.room_id = r.id
        LEFT JOIN beds b ON tb.bed_id = b.id
        WHERE gp.organization_id = ?
    ";
    $params = [$orgId];

    if ($activeBldId) {
        $sql .= " AND (gp.building_id = ? OR gp.building_id IS NULL)";
        $params[] = $activeBldId;
    }
    if (!empty($tenantId)) {
        $sql .= " AND gp.tenant_id = ?";
        $params[] = (int)$tenantId;
    }
    if (!empty($status)) {
        $sql .= " AND gp.status = ?";
        $params[] = $status;
    }

    $sql .= " ORDER BY gp.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $passes = $stmt->fetchAll();

    sendJSON(['success' => true, 'data' => $passes]);
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $tenantId = ($user['role'] === 'tenant') ? $user['id'] : (int)($input['tenant_id'] ?? $user['id']);
    $passType = $input['pass_type'] ?? 'night_out';
    $outDatetime = trim($input['out_datetime'] ?? ($input['out_time'] ?? ''));
    $expectedInDatetime = trim($input['expected_in_datetime'] ?? ($input['expected_in_time'] ?? ''));
    $reason = trim($input['reason'] ?? '');
    $destination = trim($input['destination'] ?? '');

    if (empty($outDatetime) || empty($expectedInDatetime) || empty($reason) || empty($destination)) {
        sendJSON(['success' => false, 'message' => 'Out time, return time, reason, and destination are required.'], 400);
    }

    // Verify tenant belongs to current organization
    $stmtTenant = $db->prepare("SELECT id, building_id FROM users WHERE id = ? AND organization_id = ?");
    $stmtTenant->execute([$tenantId, $orgId]);
    $tRow = $stmtTenant->fetch();
    if (!$tRow) {
        sendJSON(['success' => false, 'message' => 'Resident not found or unauthorized.'], 404);
    }
    $bldId = $tRow['building_id'] ?? TenantContext::getBuildingId();

    require_once __DIR__ . '/../../services/GatePassService.php';
    $passCode = generateCode('GP');
    $qrToken = GatePassService::generateQrToken();

    $stmt = $db->prepare("
        INSERT INTO gatepasses (organization_id, building_id, pass_code, qr_token, tenant_id, pass_type, out_datetime, expected_in_datetime, out_time, expected_in_time, reason, destination, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$orgId, $bldId, $passCode, $qrToken, $tenantId, $passType, $outDatetime, $expectedInDatetime, $outDatetime, $expectedInDatetime, $reason, $destination]);
    $passId = $db->lastInsertId();

    // Dispatch notification to Admin Console
    NotificationService::sendToAdmin(
        $orgId,
        $bldId ? (int)$bldId : null,
        'GATEPASS_REQUEST',
        'Gate Pass Request',
        "New " . str_replace('_', ' ', $passType) . " gate pass request ({$passCode}) submitted.",
        'gatepass.php',
        'medium',
        ['pass_id' => $passId, 'pass_code' => $passCode]
    );

    sendJSON([
        'success' => true,
        'message' => "Gate Pass request submitted! Ref Code: {$passCode}",
        'pass_code' => $passCode,
        'gatepass_id' => $passId
    ]);
}
