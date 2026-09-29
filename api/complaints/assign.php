<?php
/**
 * API: Complaints - Assign Technician/Staff
 */

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'POST method required.'], 405);
}

$admin = requireAuth('admin');
$orgId = TenantContext::getOrgId();
$db = getDB();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$complaintId = (int)($input['complaint_id'] ?? 0);
$assignedTo = trim($input['assigned_to'] ?? '');

if ($complaintId <= 0 || empty($assignedTo)) {
    sendJSON(['success' => false, 'message' => 'Complaint ID and Assigned Staff Name are required.'], 400);
}

$stmt = $db->prepare("UPDATE complaints SET assigned_to = ?, status = IF(status='open', 'in_progress', status) WHERE id = ? AND organization_id = ?");
$stmt->execute([$assignedTo, $complaintId, $orgId]);

if ($stmt->rowCount() === 0 && !TenantContext::isSuperAdmin()) {
    sendJSON(['success' => false, 'message' => 'Complaint not found or unauthorized.'], 404);
}

sendJSON([
    'success' => true,
    'message' => "Complaint #{$complaintId} assigned to {$assignedTo} successfully!",
    'assigned_to' => $assignedTo
]);
