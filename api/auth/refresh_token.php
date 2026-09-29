<?php
/**
 * API: Authentication - Refresh Token & Check Session
 */

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$user = getAuthUser();

if (!$user) {
    // Check bearer token in Authorization header
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        $db = getDB();
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.email, u.phone, u.role, u.avatar, u.status 
            FROM refresh_tokens rt
            JOIN users u ON rt.user_id = u.id
            WHERE rt.token = ? AND rt.expires_at > NOW() AND u.status != 'blocked'
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_phone'] = $user['phone'];
        }
    }
}

if (!$user) {
    sendJSON(['success' => false, 'authenticated' => false, 'message' => 'Session expired. Please log in.'], 401);
}

sendJSON([
    'success' => true,
    'authenticated' => true,
    'user' => $user
]);
