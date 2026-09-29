<?php
/**
 * API: Staff Gate Pass - Mark Return Movement
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/GatePassService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUser = getAuthUser();
if (!$currentUser) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Please log in.'], 401);
}

$orgId = TenantContext::getOrgId();
if (!$orgId) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: No organization context.'], 401);
}

$activeBldId = TenantContext::getBuildingId();
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$gatePassId = (int)($input['gatepass_id'] ?? $input['id'] ?? 0);
$notes = trim((string)($input['notes'] ?? ''));

if ($gatePassId <= 0) {
    sendJSON(['success' => false, 'message' => 'Valid Gate Pass ID is required.'], 400);
}

$staffUserId = (int)$currentUser['id'];
$staffName = $currentUser['name'] ?: 'Staff Member';

$res = GatePassService::markReturned($gatePassId, $orgId, $staffUserId, $staffName, $activeBldId, $notes);

sendJSON($res, $res['success'] ? 200 : 400);