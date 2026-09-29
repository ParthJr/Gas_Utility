<?php
/**
 * API: Complaints - Status Updater (Kanban Drag-and-Drop) & Board Data
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/EntitlementService.php';

header('Content-Type: application/json; charset=utf-8');

$orgId = TenantContext::getOrgId();
EntitlementService::requireAnyFeature($orgId, ['maintenance_requests', 'maintenance_ticket_management', 'maintenance_kanban_board', 'maintenance_status_tracking']);

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    if (!$orgId && !TenantContext::isSuperAdmin()) {
        sendJSON(['success' => false, 'message' => 'Unauthorized context'], 401);
    }
    $tenantId = $_GET['tenant_id'] ?? null;
    $status = $_GET['status'] ?? null;
    $activeBldId = TenantContext::getBuildingId();

    $sql = "
        SELECT c.*, u.name as tenant_name, u.phone as tenant_phone, r.room_number
        FROM complaints c
        JOIN users u ON c.tenant_id = u.id
        LEFT JOIN rooms r ON c.room_id = r.id
        WHERE c.organization_id = ?
    ";
    $params = [$orgId];

    if ($activeBldId) {
        $sql .= " AND (c.building_id = ? OR c.building_id IS NULL)";
        $params[] = $activeBldId;
    }
    if (!empty($tenantId)) {
        $sql .= " AND c.tenant_id = ?";
        $params[] = (int)$tenantId;
    }
    if (!empty($status)) {
        $sql .= " AND c.status = ?";
        $params[] = $status;
    }

    $sql .= " ORDER BY c.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    // Group by status for Kanban
    $kanban = [
        'open' => [],
        'in_progress' => [],
        'resolved' => [],
        'closed' => []
    ];

    foreach ($items as $item) {
        $st = $item['status'];
        if (isset($kanban[$st])) {
            $kanban[$st][] = $item;
        } else {
            $kanban['open'][] = $item;
        }
    }

    sendJSON([
        'success' => true,
        'total' => count($items),
        'items' => $items,
        'kanban' => $kanban
    ]);
}

$admin = requireAuth('admin');

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $complaintId = (int)($input['complaint_id'] ?? 0);
    $newStatus = $input['status'] ?? 'in_progress';
    $remarks = trim($input['admin_remarks'] ?? ($input['resolution_notes'] ?? ''));

    $res = MaintenanceService::updateTicket($orgId, $complaintId, [
        'status' => $newStatus,
        'resolution_notes' => $remarks,
        'admin_remarks' => $remarks
    ]);

    if ($res['success']) {
        sendJSON([
            'success' => true,
            'message' => $res['message'],
            'complaint_id' => $complaintId,
            'status' => $newStatus
        ]);
    } else {
        sendJSON(['success' => false, 'message' => $res['message']], 400);
    }
}
