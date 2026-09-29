<?php
/**
 * API: Complaints - Create Ticket
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/EntitlementService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'POST method required.'], 405);
}

$user = requireAuth();
$orgId = TenantContext::getOrgId();
EntitlementService::requireAnyFeature($orgId, ['maintenance_requests', 'maintenance_ticket_management', 'maintenance_kanban_board']);

$db = getDB();

require_once __DIR__ . '/../../services/MaintenanceService.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$tenantId = ($user['role'] === 'tenant') ? (int)$user['id'] : (int)($input['tenant_id'] ?? $user['id']);
$title = trim($input['title'] ?? '');
$description = trim($input['description'] ?? '');
$category = $input['category'] ?? 'other';
$priority = $input['priority'] ?? 'medium';
$idempotencyKey = trim((string)($input['idempotency_key'] ?? ($input['submission_token'] ?? '')));

if (empty($title) || empty($description)) {
    sendJSON(['success' => false, 'message' => 'Title and description are required.'], 400);
}

// ── SERVER-SIDE IDEMPOTENCY GUARD ──────────────────────────────────────────
// If the client did not supply an idempotency key, generate one from a
// content-hash so that duplicate form submits or page refreshes can never
// create two identical tickets. The key hashes org + tenant + date + title
// so re-submissions on the SAME day are blocked, but the same tenant can
// open a new ticket for the same problem on a different day.
if (empty($idempotencyKey)) {
    $idempotencyKey = hash('sha256',
        $orgId . ':' . $tenantId . ':' . date('Y-m-d') . ':' . strtolower(trim($title))
    );
}
// ──────────────────────────────────────────────────────────────────────────

// Handle file upload if any
$attachment = null;
if (!empty($_FILES['attachment']['name'])) {
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }
    $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
    $filename = 'complaint_' . time() . '_' . uniqid() . '.' . $ext;
    $target = UPLOAD_PATH . $filename;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
        $attachment = $filename;
    }
}

$assignedTo = trim($input['assigned_to'] ?? '');

$res = MaintenanceService::createTicket($orgId, TenantContext::getBuildingId(), $tenantId, [
    'title' => $title,
    'description' => $description,
    'category' => $category,
    'priority' => $priority,
    'idempotency_key' => $idempotencyKey ?: null,
    'assigned_to' => $assignedTo ?: null,
    'attachment' => $attachment
]);

sendJSON($res, $res['success'] ? 200 : 400);
