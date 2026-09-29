<?php
declare(strict_types=1);

/**
 * StayFlow PG/Property Management SaaS — Push Subscription Endpoint
 * Registers or unregisters browser push subscriptions.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/PushNotificationService.php';

$authUser = getAuthUser();
if (!$authUser) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Please log in.'], 401);
}

$userId = (int)$authUser['id'];
$orgId = TenantContext::getOrgId() ?: (!empty($authUser['organization_id']) ? (int)$authUser['organization_id'] : 1);
$buildingId = TenantContext::getBuildingId() ?: (!empty($authUser['building_id']) ? (int)$authUser['building_id'] : null);

$userRole = strtolower((string)($authUser['role_code'] ?? ($authUser['role'] ?? ($_SESSION['user_role_code'] ?? ($_SESSION['user_role'] ?? 'resident')))));
$standardRole = in_array($userRole, ['admin', 'super_admin', 'superadmin', 'owner', 'manager']) ? 'admin' : (in_array($userRole, ['staff', 'security', 'warden']) ? 'staff' : 'resident');

$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);

if (!is_array($data)) {
    sendJSON(['success' => false, 'message' => 'Invalid JSON payload'], 400);
}

$action = $data['action'] ?? 'subscribe';

if ($action === 'unsubscribe') {
    $endpoint = trim($data['endpoint'] ?? '');
    if (empty($endpoint)) {
        sendJSON(['success' => false, 'message' => 'Endpoint required for unsubscribe'], 400);
    }
    PushNotificationService::unsubscribe($userId, $endpoint);
    sendJSON(['success' => true, 'message' => 'Unsubscribed successfully']);
}

// Subscription payload registration
$subscription = $data['subscription'] ?? $data;
$platform = $data['platform'] ?? 'web';
$browser = $data['browser'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? null);
$deviceName = $data['device_name'] ?? null;

$res = PushNotificationService::registerSubscription(
    $userId,
    $orgId,
    $buildingId,
    $standardRole,
    $subscription,
    $platform,
    $browser ? substr($browser, 0, 50) : null,
    $deviceName
);

if ($res['success']) {
    sendJSON([
        'success' => true,
        'message' => 'Device subscribed for push notifications successfully.'
    ]);
} else {
    sendJSON([
        'success' => false,
        'message' => $res['message']
    ], 400);
}
