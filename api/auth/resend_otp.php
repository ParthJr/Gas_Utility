<?php
/**
 * StayFlow SaaS Platform — Resend OTP API Endpoint
 *
 * Dispatches a new 6-digit verification code with strict 60-second
 * server-side rate limit and cooldown enforcement.
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

$pending = $_SESSION['otp_pending'] ?? null;
if (empty($pending) || empty($pending['email']) || empty($pending['purpose'])) {
    sendJSON([
        'success' => false,
        'message' => 'No active verification session found. Please sign in or register again.',
        'redirect' => 'login.php'
    ], 401);
}

$email = $pending['email'];
$purpose = $pending['purpose'];
$userId = !empty($pending['user_id']) ? (int)$pending['user_id'] : null;
$name = $pending['name'] ?? '';

// 1. Create New OTP with Server-Side Cooldown Check
$otpResult = OTPService::createOTP($email, $purpose, $userId);

if (!$otpResult['success']) {
    $statusCode = ($otpResult['error'] === 'cooldown_active' || $otpResult['error'] === 'rate_limited') ? 429 : 400;
    sendJSON([
        'success' => false,
        'error' => $otpResult['error'] ?? 'cooldown_active',
        'seconds_left' => $otpResult['seconds_left'] ?? 60,
        'message' => $otpResult['message']
    ], $statusCode);
}

// 2. Dispatch via EmailService
if ($purpose === OTPService::PURPOSE_SIGNUP) {
    EmailService::sendVerificationOTP($email, $otpResult['otp'], $name);
} elseif ($purpose === OTPService::PURPOSE_LOGIN) {
    EmailService::sendLoginOTP($email, $otpResult['otp'], $name);
} elseif ($purpose === OTPService::PURPOSE_PASSWORD_RESET) {
    EmailService::sendPasswordResetOTP($email, $otpResult['otp'], $name);
}

AuditService::log('otp_resent', 'user', $userId ?: 0, null, ['email' => $email, 'purpose' => $purpose], 0, null, $userId ?: 0);

sendJSON([
    'success' => true,
    'message' => 'A fresh 6-digit code has been dispatched to your email.',
    'seconds_left' => 60,
    'masked_email' => OTPService::maskEmail($email)
]);
