<?php
/**
 * API: Staff Gate Pass QR Scanner - Lookup & Verification Endpoint
 * Resolves QR Token or Pass Code with strict tenant and building isolation.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/GatePassService.php';

header('Content-Type: application/json; charset=utf-8');

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
$identifier = trim((string)($_GET['token'] ?? $_GET['code'] ?? $_GET['pass_code'] ?? ''));

if (empty($identifier)) {
    sendJSON(['success' => false, 'message' => 'Invalid request: QR Token or Gate Pass Code required.'], 400);
}

$pass = GatePassService::lookupGatePass($identifier, $orgId, $activeBldId);

if (!$pass) {
    sendJSON([
        'success' => false,
        'message' => 'Invalid Gate Pass. This QR code or pass code is not recognized in your organization.',
        'error_type' => 'INVALID_PASS'
    ], 404);
}

sendJSON([
    'success' => true,
    'message' => 'Gate Pass verified successfully.',
    'data' => $pass
]);