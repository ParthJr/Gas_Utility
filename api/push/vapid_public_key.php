<?php
declare(strict_types=1);

/**
 * StayFlow PG/Property Management SaaS — VAPID Public Key API Endpoint
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../../services/WebPushService.php';

try {
    $publicKey = WebPushService::getPublicKey();
    echo json_encode([
        'success' => true,
        'public_key' => $publicKey
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
