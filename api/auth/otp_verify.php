<?php
/**
 * API: Authentication - Mobile OTP Generator & Verifier
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/DLTNotificationService.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $input['action'] ?? 'request_otp'; // request_otp OR verify_otp
$phone = trim($input['phone'] ?? '');

if (empty($phone)) {
    sendJSON(['success' => false, 'message' => 'Phone number is required.'], 400);
}

$db = getDB();

if ($action === 'request_otp') {
    // Check if user exists
    $stmt = $db->prepare("SELECT id, name, role FROM users WHERE phone = ? LIMIT 1");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user) {
        sendJSON(['success' => false, 'message' => 'No account registered with this phone number.'], 404);
    }

    // Fixed / Demo OTP for development, or dynamic random 6-digit OTP
    $otp = '123456';
    $_SESSION['otp_' . $phone] = [
        'code' => $otp,
        'expires' => time() + 600 // 10 minutes
    ];

    // Trigger DLT SMS
    DLTNotificationService::sendOTP($phone, $otp);

    sendJSON([
        'success' => true,
        'message' => "OTP sent successfully to {$phone}. (For Demo/Testing: 123456)",
        'demo_otp' => $otp
    ]);
} elseif ($action === 'verify_otp') {
    $enteredOtp = trim($input['otp'] ?? '');
    
    if (empty($enteredOtp)) {
        sendJSON(['success' => false, 'message' => 'OTP is required.'], 400);
    }

    $cached = $_SESSION['otp_' . $phone] ?? null;

    // Allow 123456 in dev/demo or match stored session OTP
    $isDemoAllowed = (defined('APP_ENV') && APP_ENV !== 'production') && $enteredOtp === '123456';
    if (($cached && $cached['code'] === $enteredOtp && time() <= $cached['expires']) || $isDemoAllowed) {
        $stmt = $db->prepare("SELECT id, name, email, phone, role, avatar, status FROM users WHERE phone = ? LIMIT 1");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        if (!$user) {
            sendJSON(['success' => false, 'message' => 'User not found.'], 404);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_phone'] = $user['phone'];

        unset($_SESSION['otp_' . $phone]);

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

        $stmtToken = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmtToken->execute([$user['id'], $token, $expiresAt]);

        $redirectUrl = ($user['role'] === 'admin' || $user['role'] === 'manager') 
            ? '/admin/index.php' 
            : '/tenant/index.php';

        sendJSON([
            'success' => true,
            'message' => 'OTP Verified Successfully!',
            'token' => $token,
            'user' => $user,
            'redirect' => $redirectUrl
        ]);
    } else {
        sendJSON(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Invalid action.'], 400);
}
