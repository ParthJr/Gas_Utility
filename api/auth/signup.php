<?php
/**
 * StayFlow SaaS Platform — Signup API Endpoint
 *
 * Registers new property management account in pending/inactive state,
 * creates tenant organization, generates secure 6-digit email OTP,
 * and initiates verification session.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/OTPService.php';
require_once __DIR__ . '/../../services/EmailService.php';
require_once __DIR__ . '/../../services/AuditService.php';
require_once __DIR__ . '/../../services/ReferralService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$name = trim($input['name'] ?? '');
$email = strtolower(trim($input['email'] ?? ''));
$password = (string)($input['password'] ?? '');
$phone = trim($input['phone'] ?? '');
$refCode = trim((string)($input['ref_code'] ?? $input['ref'] ?? $_SESSION['ref_code'] ?? ''));
$cycleParam = strtolower(trim((string)($input['cycle'] ?? $_SESSION['billing_cycle'] ?? 'monthly')));
if ($cycleParam !== 'yearly') {
    $cycleParam = 'monthly';
}
$_SESSION['billing_cycle'] = $cycleParam;

// 1. Input Validation
if (empty($name) || strlen($name) < 2) {
    sendJSON(['success' => false, 'message' => 'Please enter a valid full name (minimum 2 characters).'], 422);
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJSON(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
}

if (strlen($password) < 8) {
    sendJSON(['success' => false, 'message' => 'Password must be at least 8 characters in length.'], 422);
}

$db = getDB();

// 2. Check for existing active accounts
$stmtUser = $db->prepare("SELECT id, status, organization_id FROM users WHERE email = ? LIMIT 1");
$stmtUser->execute([$email]);
$existingUser = $stmtUser->fetch();

$userId = null;
$orgId = null;

if ($existingUser) {
    if ($existingUser['status'] === 'active') {
        sendJSON([
            'success' => false,
            'message' => 'An active account with this email already exists. Please log in.'
        ], 409);
    }
    
    // Account exists but is inactive/unverified: update password and details
    $userId = (int)$existingUser['id'];
    $orgId = (int)($existingUser['organization_id'] ?? 0);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $cleanPhone = !empty($phone) ? preg_replace('/[^0-9]/', '', $phone) : null;

    $stmtUpdate = $db->prepare("
        UPDATE users 
        SET name = ?, password = ?, phone = COALESCE(?, phone), updated_at = NOW() 
        WHERE id = ?
    ");
    $stmtUpdate->execute([$name, $hashedPassword, $cleanPhone, $userId]);

    $planParam = trim((string)($input['plan'] ?? $_SESSION['selected_plan'] ?? ''));
    if (!empty($planParam) && $orgId > 0) {
        $stmtPCheck = $db->prepare("SELECT id FROM plans WHERE (plan_slug = ? OR plan_code = ? OR id = ?) AND status = 'active' LIMIT 1");
        $stmtPCheck->execute([$planParam, strtoupper($planParam), (int)$planParam]);
        $pId = $stmtPCheck->fetchColumn();
        if ($pId) {
            $db->prepare("UPDATE organizations SET plan_id = ?, updated_at = NOW() WHERE id = ?")->execute([$pId, $orgId]);
        }
    }
} else {
    // 3. Create new Organization and User
    try {
        $db->beginTransaction();

        $cleanPhone = !empty($phone) ? preg_replace('/[^0-9]/', '', $phone) : null;
        $orgCode = 'ORG-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 6)) . '-' . random_int(1000, 9999);
        $companyName = $name . "'s Property";

        // Secure Plan Resolution from Database (Super Admin is single source of truth)
        $planParam = trim((string)($input['plan'] ?? $_SESSION['selected_plan'] ?? ''));
        $planId = 1;
        $trialDays = 14;

        if (!empty($planParam)) {
            $stmtPlan = $db->prepare("SELECT id, trial_days, price_monthly, price_yearly FROM plans WHERE (plan_slug = ? OR plan_code = ? OR id = ?) AND status = 'active' LIMIT 1");
            $stmtPlan->execute([$planParam, strtoupper($planParam), (int)$planParam]);
            $resolvedPlan = $stmtPlan->fetch();
            if ($resolvedPlan) {
                $planId = (int)$resolvedPlan['id'];
                $trialDays = !empty($resolvedPlan['trial_days']) ? (int)$resolvedPlan['trial_days'] : 14;
            }
        } else {
            $stmtDefault = $db->query("SELECT id, trial_days, price_monthly, price_yearly FROM plans WHERE status = 'active' ORDER BY display_order ASC, id ASC LIMIT 1");
            $defaultPlan = $stmtDefault->fetch();
            if ($defaultPlan) {
                $resolvedPlan = $defaultPlan;
                $planId = (int)$defaultPlan['id'];
                $trialDays = !empty($defaultPlan['trial_days']) ? (int)$defaultPlan['trial_days'] : 14;
            }
        }

        // Create organization with verified plan_id
        $stmtOrg = $db->prepare("
            INSERT INTO organizations (organization_code, company_name, owner_name, email, phone, plan_id, subscription_status, trial_start, trial_end, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'trial', CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? DAY), NOW())
        ");
        $stmtOrg->execute([$orgCode, $companyName, $name, $email, $cleanPhone, $planId, $trialDays]);
        $orgId = (int)$db->lastInsertId();

        // Create initial trial subscription record with selected billing cycle
        $cycleAmount = ($cycleParam === 'yearly') ? (float)($resolvedPlan['price_yearly'] ?? 0) : (float)($resolvedPlan['price_monthly'] ?? 0);
        $stmtSub = $db->prepare("
            INSERT INTO subscriptions (organization_id, plan_id, custom_plan_id, billing_cycle, amount, status, start_date, end_date, next_billing_date, created_at, updated_at)
            VALUES (?, ?, NULL, ?, ?, 'active', CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? DAY), DATE_ADD(CURDATE(), INTERVAL ? DAY), NOW(), NOW())
        ");
        $stmtSub->execute([$orgId, $planId, $cycleParam, $cycleAmount, $trialDays, $trialDays]);

        // Find Owner role
        $stmtRole = $db->prepare("SELECT id FROM roles WHERE role_code = 'owner' LIMIT 1");
        $stmtRole->execute();
        $roleId = (int)($stmtRole->fetchColumn() ?: 2);

        // Clean phone or null
        $cleanPhone = !empty($phone) ? preg_replace('/[^0-9]/', '', $phone) : null;
        if (!empty($cleanPhone)) {
            // Check if phone already in use
            $chkPhone = $db->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
            $chkPhone->execute([$cleanPhone]);
            if ($chkPhone->fetch()) {
                $cleanPhone = null; // Don't collide on duplicate optional phone
            }
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert inactive user awaiting email OTP verification
        $stmtCreateUser = $db->prepare("
            INSERT INTO users (organization_id, name, email, phone, password, role, role_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'admin', ?, 'inactive', NOW())
        ");
        $stmtCreateUser->execute([$orgId, $name, $email, $cleanPhone, $hashedPassword, $roleId]);
        $userId = (int)$db->lastInsertId();

        $db->commit();

        if (!empty($refCode)) {
            ReferralService::recordReferral($refCode, $userId, $orgId);
        }
    } catch (\Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("[Signup Error] Failed to create user: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Registration could not be completed. Please try again.'], 500);
    }
}

// 4. Generate Signup Verification OTP
$otpResult = OTPService::createOTP($email, OTPService::PURPOSE_SIGNUP, $userId);

if (!$otpResult['success']) {
    sendJSON([
        'success' => false,
        'message' => $otpResult['message']
    ], ($otpResult['error'] === 'cooldown_active' || $otpResult['error'] === 'rate_limited') ? 429 : 400);
}

// 5. Dispatch Branded Verification Email
$emailSent = EmailService::sendVerificationOTP($email, $otpResult['otp'], $name);

// 6. Set Server-Side Pending OTP Session Context
$_SESSION['otp_pending'] = [
    'user_id' => $userId,
    'email' => $email,
    'purpose' => OTPService::PURPOSE_SIGNUP,
    'name' => $name,
    'organization_id' => $orgId
];

AuditService::log('signup_otp_dispatched', 'user', $userId, null, ['email' => $email], $orgId, null, $userId);

sendJSON([
    'success' => true,
    'message' => 'A 6-digit verification code has been dispatched to your email.',
    'redirect' => '/verify-otp',
    'redirect_url' => '/verify-otp',
    'masked_email' => OTPService::maskEmail($email)
]);
