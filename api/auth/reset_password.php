<?php
/**
 * StayFlow SaaS Platform — Reset Password API Endpoint
 *
 * Updates user password following successful 6-digit OTP verification.
 * Enforces server-side authorization token from $_SESSION['password_reset_authorized'].
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verify Server-Side Reset Authorization
$auth = $_SESSION['password_reset_authorized'] ?? null;
if (empty($auth) || empty($auth['user_id'])) {
    sendJSON([
        'success' => false,
        'message' => 'Password reset authorization has expired or is invalid. Please request a new verification code.',
        'redirect' => 'forgot_password.php'
    ], 401);
}

// Check 10-minute window for password change
if (!empty($auth['authorized_at']) && (time() - (int)$auth['authorized_at']) > 600) {
    unset($_SESSION['password_reset_authorized']);
    sendJSON([
        'success' => false,
        'message' => 'Password reset session has expired. Please request a new verification code.',
        'redirect' => 'forgot_password.php'
    ], 401);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$password = (string)($input['password'] ?? '');
$confirmPassword = (string)($input['confirm_password'] ?? '');

if (strlen($password) < 8) {
    sendJSON(['success' => false, 'message' => 'Password must be at least 8 characters in length.'], 422);
}

if ($password !== $confirmPassword) {
    sendJSON(['success' => false, 'message' => 'Passwords do not match. Please verify and re-enter.'], 422);
}

$userId = (int)$auth['user_id'];
$db = getDB();

// 2. Update Password and Clear Security Locks
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$stmt = $db->prepare("
    UPDATE users 
    SET password = ?, failed_login_attempts = 0, locked_until = NULL, password_changed_at = NOW(), updated_at = NOW() 
    WHERE id = ?
");
$stmt->execute([$hashedPassword, $userId]);

// Clear reset authorization
unset($_SESSION['password_reset_authorized']);

AuditService::log('password_reset_completed', 'user', $userId, null, ['user_id' => $userId], 0, null, $userId);

sendJSON([
    'success' => true,
    'message' => 'Password reset successfully! You can now log in with your new password.',
    'redirect' => 'login.php',
    'redirect_url' => 'login.php'
]);
