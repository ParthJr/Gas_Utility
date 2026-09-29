<?php
/**
 * API: Authentication - Resident Email OTP Generator
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/MailService.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

// Read input
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($input['email'] ?? '');

if (empty($email)) {
    sendJSON(['success' => false, 'message' => 'Email address is required.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJSON(['success' => false, 'message' => 'Invalid email address format.'], 400);
}

$db = getDB();
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// 1. Cooldown Check (60 seconds)
$stmtCooldown = $db->prepare("
    SELECT created_at FROM resident_login_otps 
    WHERE (email = ? OR ip_address = ?) AND created_at > DATE_SUB(NOW(), INTERVAL 60 SECOND)
    ORDER BY id DESC LIMIT 1
");
$stmtCooldown->execute([$email, $ip]);
$lastRequest = $stmtCooldown->fetchColumn();
if ($lastRequest) {
    $secondsPassed = time() - strtotime($lastRequest);
    $secondsLeft = 60 - $secondsPassed;
    if ($secondsLeft > 0) {
        sendJSON([
            'success' => false, 
            'message' => "Please wait {$secondsLeft} seconds before requesting a new OTP."
        ], 429);
    }
}

// 2. Email Rate Limit Check (5 requests per hour)
$stmtEmailRate = $db->prepare("
    SELECT COUNT(*) FROM resident_login_otps 
    WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
");
$stmtEmailRate->execute([$email]);
$emailRequests = (int)$stmtEmailRate->fetchColumn();
if ($emailRequests >= 5) {
    AuditService::log('otp_rate_limited', 'user', 0, null, ['email' => $email, 'type' => 'email_rate'], 1);
    sendJSON([
        'success' => false, 
        'message' => 'Too many OTP requests. Please try again later.'
    ], 429);
}

// 3. IP Rate Limit Check (5 requests per hour)
$stmtIpRate = $db->prepare("
    SELECT COUNT(*) FROM resident_login_otps 
    WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
");
$stmtIpRate->execute([$ip]);
$ipRequests = (int)$stmtIpRate->fetchColumn();
if ($ipRequests >= 10) { // A slightly higher threshold for shared IP environments, but strictly capped
    AuditService::log('otp_rate_limited', 'user', 0, null, ['ip' => $ip, 'type' => 'ip_rate'], 1);
    sendJSON([
        'success' => false, 
        'message' => 'Too many OTP requests. Please try again later.'
    ], 429);
}

// Generic success message to prevent account enumeration
$genericSuccess = [
    'success' => true,
    'message' => 'If the email is registered, an OTP has been sent.'
];

// 4. Locate active Resident/Tenant account
$stmtResident = $db->prepare("
    SELECT id, name, status, organization_id 
    FROM users 
    WHERE email = ? AND role = 'tenant' AND status = 'active'
    LIMIT 1
");
$stmtResident->execute([$email]);
$resident = $stmtResident->fetch();

// Account Enumeration Protection: If not active or doesn't exist, exit quietly with generic success response
if (!$resident) {
    sendJSON($genericSuccess);
}

// 5. Generate secure random 6-digit OTP
try {
    $otpCode = (string)random_int(100000, 999999);
} catch (Exception $e) {
    $otpCode = (string)mt_rand(100000, 999999); // Fallback if random_int fails
}

// Hash OTP
$otpHash = password_hash($otpCode, PASSWORD_BCRYPT);

// 6. Save in OTP table
$stmtInsert = $db->prepare("
    INSERT INTO resident_login_otps (resident_id, tenant_organization_id, email, otp_hash, expires_at, attempt_count, max_attempts, last_sent_at, ip_address, user_agent)
    VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE), 0, 5, NOW(), ?, ?)
");
$stmtInsert->execute([
    (int)$resident['id'], 
    (int)$resident['organization_id'], 
    $email, 
    $otpHash, 
    $ip, 
    $ua
]);
$otpRecordId = (int)$db->lastInsertId();

// 7. Format Professional OTP Email Template
$subject = "Your PG-Core Engine Resident Login OTP";
$body = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b;'>
    <div style='background-color: #4f46e5; padding: 15px; border-radius: 6px 6px 0 0; text-align: center; color: white;'>
        <h2 style='margin: 0; font-size: 20px; font-weight: bold;'>PG-Core Engine</h2>
    </div>
    <div style='padding: 20px; line-height: 1.6;'>
        <p>Hello <strong>" . htmlspecialchars($resident['name']) . "</strong>,</p>
        <p>You requested a one-time verification code to log in to the Resident App.</p>
        <div style='background-color: #f1f5f9; padding: 15px; border-radius: 6px; text-align: center; margin: 20px 0;'>
            <span style='font-size: 28px; font-weight: bold; letter-spacing: 4px; color: #4f46e5;'>" . $otpCode . "</span>
        </div>
        <p>This OTP is valid for <strong>5 minutes</strong>.</p>
        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
        <p style='font-size: 12px; color: #64748b;'>
            <strong>For your security:</strong><br>
            * Do not share this OTP with anyone.<br>
            * PG-Core Engine support will never ask for your OTP.<br>
            * If you did not request this code, you can safely ignore this email.
        </p>
    </div>
    <div style='background-color: #f8fafc; padding: 12px; text-align: center; border-radius: 0 0 6px 6px; font-size: 11px; color: #94a3b8;'>
        Regards,<br>
        <strong>PG-Core Engine Resident Support</strong>
    </div>
</div>
";

// 8. Deliver Email via MailService
$sent = MailService::send($email, $subject, $body);

if (!$sent) {
    // Invalidate / cleanup pending unverified OTP to prevent orphaned states
    $db->prepare("DELETE FROM resident_login_otps WHERE id = ?")->execute([$otpRecordId]);

    AuditService::log('otp_delivery_failed', 'user', $resident['id'], null, [
        'email' => MailService::maskEmail($email),
        'reason' => MailService::getLastError()
    ], $resident['organization_id']);

    $isDev = (defined('APP_ENV') && (APP_ENV === 'local' || APP_ENV === 'development'));
    $response = [
        'success' => false,
        'message' => "We couldn't send the verification code right now. Please try again."
    ];

    if ($isDev) {
        $response['dev_diagnostics'] = [
            'error' => MailService::getLastError(),
            'smtp_host' => defined('MAIL_HOST') ? MAIL_HOST : 'localhost',
            'smtp_port' => defined('MAIL_PORT') ? MAIL_PORT : 587,
            'smtp_user_set' => !empty(MAIL_USERNAME),
            'smtp_pass_set' => !empty(MAIL_PASSWORD),
            'hint' => 'Configure valid SMTP credentials (e.g. Gmail App Password or Brevo) in config/constants.php'
        ];
    }

    sendJSON($response, 500);
}

AuditService::log('otp_requested', 'user', $resident['id'], null, ['email' => MailService::maskEmail($email)], $resident['organization_id']);

sendJSON($genericSuccess);
