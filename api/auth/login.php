<?php
/**
 * API: Authentication - Multi-Tenant & RBAC User Login
 */

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$identifier = trim($input['identifier'] ?? $input['username'] ?? $input['phone'] ?? $input['email'] ?? '');
$password = trim($input['password'] ?? '');

$authResult = AuthService::attemptLogin($identifier, $password);

if (!$authResult['success']) {
    sendJSON($authResult, 401);
}

$user = $authResult['user'];

// Generate API token
$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
$db = getDB();
$stmtToken = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
$stmtToken->execute([$user['id'], $token, $expiresAt]);

sendJSON([
    'success' => true,
    'message' => 'Login successful! Redirecting...',
    'token' => $token,
    'user' => $user,
    'redirect' => $authResult['redirect']
]);
