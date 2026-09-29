<?php
declare(strict_types=1);

/**
 * StayFlow PG/Property Management SaaS — Send Test Push API
 * Allows logged-in users to verify their device push subscription immediately.
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

$result = PushNotificationService::sendTestPush($userId);

if ($result['total'] === 0) {
    sendJSON([
        'success' => false,
        'message' => 'No active push subscriptions found for your account on this device. Please enable notifications first.'
    ], 404);
}

sendJSON([
    'success' => true,
    'message' => "Push notification sent! Dispatched to {$result['sent']} active device(s).",
    'stats' => $result
]);
