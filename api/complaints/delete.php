<?php
/**
 * StayFlow API — Maintenance Tickets Deletion
 * Permanently deletes maintenance tickets with strict multi-tenant authorization
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/EntitlementService.php';
require_once __DIR__ . '/../../services/MaintenanceService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

$user = requireAuth();
$orgId = TenantContext::getOrgId();
EntitlementService::requireAnyFeature($orgId, ['maintenance_requests', 'maintenance_ticket_management', 'maintenance_kanban_board']);

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if (!verifyCsrfToken($csrfToken)) {
    sendJSON(['success' => false, 'message' => 'Invalid security token (CSRF). Please refresh and try again.'], 403);
}

$ticketId = (int)($input['ticket_id'] ?? ($input['id'] ?? 0));

if ($ticketId <= 0) {
    sendJSON(['success' => false, 'message' => 'Valid ticket ID is required.'], 400);
}

$res = MaintenanceService::deleteTicket($orgId, $ticketId, [
    'user_id' => (int)$user['id'],
    'role' => $user['role']
]);

if (!$res['success']) {
    $httpCode = 400;
    if (($res['code'] ?? '') === 'NOT_FOUND') {
        $httpCode = 404;
    } elseif (($res['code'] ?? '') === 'UNAUTHORIZED') {
        $httpCode = 403;
    }
    sendJSON(['success' => false, 'message' => $res['message']], $httpCode);
}

sendJSON([
    'success' => true,
    'message' => $res['message'],
    'ticket_id' => $res['ticket_id'] ?? $ticketId,
    'ticket_no' => $res['ticket_no'] ?? null
], 200);