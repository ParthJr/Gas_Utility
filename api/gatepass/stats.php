<?php
/**
 * API: Staff Gate Pass - Real-time Dashboard Statistics & Timeline
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
$stats = GatePassService::getTodayStats($orgId, $activeBldId);
$recent = GatePassService::getRecentMovements($orgId, $activeBldId, 15);
$overdue = GatePassService::getOverduePasses($orgId, $activeBldId);

sendJSON([
    'success' => true,
    'stats' => $stats,
    'recent_scans' => $recent,
    'overdue_list' => $overdue
]);