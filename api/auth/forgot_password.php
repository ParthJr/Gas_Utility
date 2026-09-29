<?php
/**
 * StayFlow SaaS Platform — Forgot Password API Endpoint
 *
 * Initiates password reset via 6-digit email OTP with strict anti-enumeration
 * protection (always returns uniform generic messaging regardless of email existence).
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/OTPService.php';
require_once __DIR__ . '/../../services/EmailService.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = strtolower(trim((string)($input['email'] ?? '')));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJSON(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
}

$genericResponse = [
    'success' => true,
    'message' => 'If an account exists with that email address, a 6-digit verification code has been dispatched.',
    'redirect' => '/verify-otp',
    'redirect_url' => '/verify-otp',
    'masked_email' => OTPService::maskEmail($email)
];

$db = getDB();

// Query user by email
$stmt = $db->prepare("SELECT id, name, status FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && $user['status'] === 'active') {
    $userId = (int)$user['id'];
    $otpResult = OTPService::createOTP($email, OTPService::PURPOSE_PASSWORD_RESET, $userId);

    if ($otpResult['success']) {
        EmailService::sendPasswordResetOTP($email, $otpResult['otp'], $user['name']);

        $_SESSION['otp_pending'] = [
            'user_id' => $userId,
            'email' => $email,
            'purpose' => OTPService::PURPOSE_PASSWORD_RESET,
            'name' => $user['name']
        ];

        AuditService::log('password_reset_otp_dispatched', 'user', $userId, null, ['email' => $email], 0, null, $userId);
    }
} else {
    // Non-existent or inactive email: populate decoy session to prevent timing and enumeration attacks
    $_SESSION['otp_pending'] = [
        'user_id' => null,
        'email' => $email,
        'purpose' => OTPService::PURPOSE_PASSWORD_RESET,
        'name' => 'User'
    ];
}

sendJSON($genericResponse);
