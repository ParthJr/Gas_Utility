<?php
/**
 * API: Gate Pass - Warden/Admin Approval & Movement Tracking
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/EntitlementService.php';
require_once __DIR__ . '/../../services/NotificationService.php';
require_once __DIR__ . '/../../services/WhatsAppMetaService.php';
require_once __DIR__ . '/../../services/DLTNotificationService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'POST method required.'], 405);
}

$admin = requireAuth('admin');
$orgId = TenantContext::getOrgId();
EntitlementService::requireAnyFeature($orgId, ['gate_pass', 'qr_gate_pass', 'visitor_management', 'check_in_out']);

$db = getDB();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$passId = (int)($input['gatepass_id'] ?? 0);
$status = $input['status'] ?? 'approved'; // approved, rejected, out, returned
$remarks = trim($input['approval_remarks'] ?? '');

if ($passId <= 0) {
    sendJSON(['success' => false, 'message' => 'Valid Gate Pass ID is required.'], 400);
}

// Fetch gate pass details + tenant & parent info (Strictly scoped to current organization)
$stmt = $db->prepare("
    SELECT gp.*, u.name as tenant_name, u.phone as tenant_phone, u.parent_name, u.parent_phone
    FROM gatepasses gp
    JOIN users u ON gp.tenant_id = u.id
    WHERE gp.id = ? AND gp.organization_id = ? LIMIT 1
");
$stmt->execute([$passId, $orgId]);
$pass = $stmt->fetch();

if (!$pass) {
    sendJSON(['success' => false, 'message' => 'Gate pass not found or unauthorized.'], 404);
}

$actualIn = ($status === 'returned') ? date('Y-m-d H:i:s') : $pass['actual_in_datetime'];

$stmtUp = $db->prepare("
    UPDATE gatepasses 
    SET status = ?, approved_by = ?, approval_remarks = ?, actual_in_datetime = ?
    WHERE id = ? AND organization_id = ?
");
$stmtUp->execute([$status, $admin['id'], $remarks, $actualIn, $passId, $orgId]);

// Send targeted in-app notification to the resident
$passTypeLabel = ucwords(str_replace('_', ' ', $pass['pass_type']));
$notifStatusTitle = "Gate Pass " . ucfirst($status);
$notifStatusMsg = "Your {$passTypeLabel} request #{$pass['pass_code']} has been marked as " . strtoupper($status) . ($remarks ? ". Remarks: {$remarks}" : ".");
NotificationService::sendToResident(
    (int)$orgId,
    (int)$pass['tenant_id'],
    'GATEPASS_UPDATE',
    $notifStatusTitle,
    $notifStatusMsg,
    'gatepass.php',
    ($status === 'rejected') ? 'high' : 'medium',
    !empty($pass['building_id']) ? (int)$pass['building_id'] : null,
    'gatepass',
    $passId
);

// Trigger parent alert if approved or checked out
if (($status === 'approved' || $status === 'out') && !empty($pass['parent_phone'])) {
    $parentName = $pass['parent_name'] ?? 'Parent/Guardian';
    $outTime = date('d M Y, h:i A', strtotime($pass['out_datetime']));
    $passTypeLabel = ucwords(str_replace('_', ' ', $pass['pass_type']));

    WhatsAppMetaService::sendParentGatepassAlert($pass['parent_phone'], $parentName, $pass['tenant_name'], $passTypeLabel, $outTime, $pass['destination']);
    DLTNotificationService::sendGatepassParentAlert($pass['parent_phone'], $pass['tenant_name'], $passTypeLabel, $outTime, $pass['destination']);

    $db->prepare("UPDATE gatepasses SET parent_alert_sent = 1 WHERE id = ?")->execute([$passId]);
}

sendJSON([
    'success' => true,
    'message' => "Gate pass #{$pass['pass_code']} marked as " . strtoupper($status) . "!",
    'status' => $status
]);
