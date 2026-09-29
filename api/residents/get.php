<?php
/**
 * API: Fetch Resident Full Details (Live Database SELECT)
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/ResidentService.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth('admin');
$orgId = TenantContext::getOrgId();
$residentId = (int)($_GET['id'] ?? 0);

if ($residentId <= 0) {
    sendJSON(['success' => false, 'message' => 'Invalid or missing resident ID.'], 400);
}

$resident = ResidentService::getResidentDetails($orgId, $residentId);

if (!$resident) {
    sendJSON(['success' => false, 'message' => 'Resident not found or access unauthorized.'], 404);
}

sendJSON([
    'success' => true,
    'data' => $resident
]);
