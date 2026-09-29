<?php
/**
 * API: Authentication - Resident Email OTP Verifier
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

// Read input
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($input['email'] ?? '');
$enteredOtp = trim($input['otp'] ?? '');

if (empty($email) || empty($enteredOtp)) {
    sendJSON(['success' => false, 'message' => 'Email address and OTP are required.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJSON(['success' => false, 'message' => 'Invalid email address format.'], 400);
}

// Check OTP pattern (exactly 6 digits)
if (!preg_match('/^\d{6}$/', $enteredOtp)) {
    sendJSON(['success' => false, 'message' => 'Invalid OTP format. Must be 6 digits.'], 400);
}

$db = getDB();

// 1. Locate Resident / Tenant Account
$stmtResident = $db->prepare("
    SELECT id, name, email, phone, role, avatar, status, organization_id 
    FROM users 
    WHERE email = ? AND role = 'tenant' AND status = 'active'
    LIMIT 1
");
$stmtResident->execute([$email]);
$user = $stmtResident->fetch();

if (!$user) {
    // To prevent account enumeration and brute-force probing, return generic invalid response
    sendJSON(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
}

// 2. Fetch the latest active, unused OTP record for this resident
$stmtOtp = $db->prepare("
    SELECT * FROM resident_login_otps 
    WHERE resident_id = ? AND used_at IS NULL AND verified_at IS NULL AND expires_at > NOW() 
    ORDER BY id DESC LIMIT 1
");
$stmtOtp->execute([(int)$user['id']]);
$otpRecord = $stmtOtp->fetch();

if (!$otpRecord) {
    sendJSON(['success' => false, 'message' => 'Your OTP has expired. Please request a new OTP.'], 400);
}

$otpId = (int)$otpRecord['id'];
$attempts = (int)$otpRecord['attempt_count'];
$maxAttempts = (int)$otpRecord['max_attempts'];

// 3. Check if limit exceeded before verifying
if ($attempts >= $maxAttempts) {
    // Explicitly mark this OTP as used/invalidated so it can't be used at all
    $db->prepare("UPDATE resident_login_otps SET used_at = NOW() WHERE id = ?")->execute([$otpId]);
    sendJSON(['success' => false, 'message' => 'Too many incorrect attempts. Please request a new OTP.'], 400);
}

// 4. Increment attempt count
$newAttempts = $attempts + 1;
$stmtIncr = $db->prepare("UPDATE resident_login_otps SET attempt_count = ? WHERE id = ?");
$stmtIncr->execute([$newAttempts, $otpId]);

// 5. Verify Hash
$isMatch = password_verify($enteredOtp, $otpRecord['otp_hash']);

if (!$isMatch) {
    AuditService::log('otp_verification_failure', 'user', $user['id'], null, ['email' => $email, 'attempt' => $newAttempts], $user['organization_id']);
    
    if ($newAttempts >= $maxAttempts) {
        // Mark used
        $db->prepare("UPDATE resident_login_otps SET used_at = NOW() WHERE id = ?")->execute([$otpId]);
        sendJSON(['success' => false, 'message' => 'Too many incorrect attempts. Please request a new OTP.'], 400);
    } else {
        sendJSON(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }
}

// 6. OTP Verification Success: Mark as used
$stmtMarkUsed = $db->prepare("UPDATE resident_login_otps SET verified_at = NOW(), used_at = NOW() WHERE id = ?");
$stmtMarkUsed->execute([$otpId]);

// 7. Establish Session Security Details
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Prevent session fixation
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_phone'] = $user['phone'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['organization_id'] = $user['organization_id'];

// 8. Generate refresh token
$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

$stmtToken = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
$stmtToken->execute([$user['id'], $token, $expiresAt]);

AuditService::log('otp_verification_success', 'user', $user['id'], null, ['email' => $email], $user['organization_id']);

sendJSON([
    'success' => true,
    'message' => 'OTP Verified Successfully!',
    'token' => $token,
    'user' => $user,
    'redirect' => '/tenant/index.php'
]);
