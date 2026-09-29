<?php
/**
 * StayFlow — Referral Auto-Qualification Endpoint
 *
 * Called when a new PG owner activates a qualifying subscription (Growth / Scale / Pro).
 * Marks the referral SUCCESSFUL and awards 1 Free Month to the referrer.
 *
 * POST params:
 *   referred_org_id  (int)    — organization ID of the newly subscribed PG owner
 *   plan_code        (string) — plan code (e.g. GROWTH, PRO)
 *   subscription_id  (int)    — optional
 *
 * Auth: super-admin session OR trusted X-Internal-Secret header.
 */

declare(strict_types=1);

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../services/ReferralService.php";
require_once __DIR__ . "/../../services/AuditService.php";

header("Content-Type: application/json; charset=utf-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "POST required."]);
    exit;
}

// Allow: super-admin session
$isSuperAdminSession = !empty($_SESSION["user_id"]) && TenantContext::isSuperAdmin();
if (!$isSuperAdminSession) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Access denied."]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

$referredOrgId  = (int)($input["referred_org_id"]  ?? 0);
$planCode       = strtoupper(trim((string)($input["plan_code"] ?? "GROWTH")));
$subscriptionId = (int)($input["subscription_id"]  ?? 0);

if ($referredOrgId <= 0) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "referred_org_id is required."]);
    exit;
}

$result = ReferralService::qualifyReferral($referredOrgId, $subscriptionId, $planCode);
echo json_encode($result);

