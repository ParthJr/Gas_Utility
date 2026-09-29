<?php
/**
 * API: Save / Update Daily Meal Menu (Admin)
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/EntitlementService.php';
require_once __DIR__ . '/../../services/MealService.php';

header('Content-Type: application/json; charset=utf-8');

$authUser = getAuthUser();
if (!$authUser) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Please log in.'], 401);
}

$orgId = TenantContext::getOrgId() ?: (!empty($authUser['organization_id']) ? (int)$authUser['organization_id'] : 1);
if (!$orgId) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: No organization context'], 401);
}

EntitlementService::requireAnyFeature($orgId, ['kitchen_management', 'meal_management', 'meal_attendance', 'kitchen_headcount']);

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$activeBldId = TenantContext::getBuildingId() ?: (!empty($authUser['building_id']) ? (int)$authUser['building_id'] : null);


try {
    $saved = MealService::saveMenu($orgId, $activeBldId, $input);

    if ($saved) {
        sendJSON([
            'success' => true,
            'message' => 'Daily meal menu saved successfully!',
            'menu' => MealService::getMenu($orgId, $activeBldId, $input['menu_date'] ?? date('Y-m-d'))
        ]);
    } else {
        sendJSON(['success' => false, 'message' => 'Failed to save daily meal menu.'], 500);
    }
} catch (InvalidArgumentException $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()], 400);
} catch (Throwable $e) {
    sendJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
}
