<?php
/**
 * StayFlow SaaS Platform — OTP Verification API Endpoint
 *
 * Verifies submitted 6-digit OTP against server-side session context.
 * Strictly prevents parameter tampering by resolving user identity and purpose
 * from server-side session state, never trusting client-supplied identifiers.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/OTPService.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Enforce Server-Side Pending Verification Context
$pending = $_SESSION['otp_pending'] ?? null;
if (empty($pending) || empty($pending['email']) || empty($pending['purpose'])) {
    sendJSON([
        'success' => false,
        'message' => 'Your verification session has expired or is invalid. Please sign in or register again.',
        'redirect' => 'login.php'
    ], 401);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$submittedOtp = trim((string)($input['otp'] ?? ''));

if (empty($submittedOtp)) {
    sendJSON(['success' => false, 'message' => 'Please enter the 6-digit verification code.'], 422);
}

$email = $pending['email'];
$purpose = $pending['purpose'];
$userId = !empty($pending['user_id']) ? (int)$pending['user_id'] : null;

// 2. Perform Verification via OTPService
$verifyRes = OTPService::verifyOTP($email, $purpose, $submittedOtp, $userId);

if (!$verifyRes['success']) {
    $statusCode = ($verifyRes['error'] === 'max_attempts_exceeded' || $verifyRes['error'] === 'expired') ? 410 : 400;
    sendJSON([
        'success' => false,
        'error' => $verifyRes['error'] ?? 'invalid_otp',
        'attempts_left' => $verifyRes['attempts_left'] ?? 0,
        'message' => $verifyRes['message']
    ], $statusCode);
}

$db = getDB();

// 3. Handle Purpose-Specific Authorization
if ($purpose === OTPService::PURPOSE_SIGNUP) {
    // Activate Account
    $stmtActivate = $db->prepare("
        UPDATE users 
        SET status = 'active', email_verified_at = NOW(), failed_login_attempts = 0, locked_until = NULL 
        WHERE id = ?
    ");
    $stmtActivate->execute([$userId]);

    // Fetch activated user details
    $stmtUser = $db->prepare("
        SELECT u.*, r.role_code, r.name as role_name, o.company_name, o.subscription_status
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN organizations o ON u.organization_id = o.id
        WHERE u.id = ? LIMIT 1
    ");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();

    if (!$user) {
        sendJSON(['success' => false, 'message' => 'User record not found.'], 500);
    }

    // Establish Authenticated Session Server-Side
    $redirectUrl = AuthService::establishSession($user);

    AuditService::log('signup_verified', 'user', $userId, null, ['email' => $email], (int)($user['organization_id'] ?? 0), null, $userId);

    sendJSON([
        'success' => true,
        'message' => 'Email verified successfully! Welcome to StayFlow.',
        'redirect' => $redirectUrl,
        'redirect_url' => $redirectUrl
    ]);

} elseif ($purpose === OTPService::PURPOSE_LOGIN) {
    // Fetch user details
    $stmtUser = $db->prepare("
        SELECT u.*, r.role_code, r.name as role_name, o.company_name, o.subscription_status
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN organizations o ON u.organization_id = o.id
        WHERE u.id = ? LIMIT 1
    ");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();

    if (!$user) {
        sendJSON(['success' => false, 'message' => 'User record not found.'], 500);
    }

    // Establish Authenticated Session Server-Side
    $redirectUrl = AuthService::establishSession($user);

    AuditService::log('login_2fa_verified', 'auth', $userId, null, ['email' => $email], (int)($user['organization_id'] ?? 0), null, $userId);

    sendJSON([
        'success' => true,
        'message' => 'Verification successful.',
        'redirect' => $redirectUrl,
        'redirect_url' => $redirectUrl
    ]);

} elseif ($purpose === OTPService::PURPOSE_PASSWORD_RESET) {
    // Authorize Password Reset in server session
    $_SESSION['password_reset_authorized'] = [
        'user_id' => $userId,
        'email' => $email,
        'authorized_at' => time()
    ];

    unset($_SESSION['otp_pending']);

    AuditService::log('password_reset_otp_verified', 'auth', $userId, null, ['email' => $email], 0, null, $userId);

    sendJSON([
        'success' => true,
        'message' => 'Verification successful. You may now choose a new password.',
        'redirect' => '/reset-password',
        'redirect_url' => '/reset-password'
    ]);
} else {
    sendJSON(['success' => false, 'message' => 'Unknown verification purpose.'], 400);
}
