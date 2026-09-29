<?php
declare(strict_types=1);

/**
 * API: Properties / Buildings - Add New Property with Plan Limit Enforcement
 */

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../services/TenantContext.php";
require_once __DIR__ . "/../../services/SubscriptionService.php";
require_once __DIR__ . "/../../services/NotificationService.php";
require_once __DIR__ . "/../../services/AuditService.php";

header("Content-Type: application/json; charset=utf-8");

$admin = requireAuth("admin");
$orgId = TenantContext::getOrgId();

if (!$orgId) {
    sendJSON(["success" => false, "message" => "Unauthorized: No organization context"], 401);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJSON(["success" => false, "message" => "Method not allowed. POST required."], 405);
}

$input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

$name = trim((string)($input["building_name"] ?? $input["name"] ?? ""));
$type = (string)($input["property_type"] ?? "co_living");
$address = trim((string)($input["address"] ?? ""));
$city = trim((string)($input["city"] ?? "Bengaluru"));
$state = trim((string)($input["state"] ?? "Karnataka"));
$pincode = trim((string)($input["pincode"] ?? "560001"));
$contactPhone = trim((string)($input["contact_phone"] ?? $input["phone"] ?? ""));
$managerName = trim((string)($input["manager_name"] ?? ""));
$floors = max(1, min(15, (int)($input["total_floors"] ?? 1)));
$curfew = (string)($input["curfew_time"] ?? "22:30:00");
$upiVpa = trim((string)($input["upi_vpa"] ?? "property@okhdfcbank"));
$upiPayee = trim((string)($input["upi_payee_name"] ?? $name));

if (empty($name)) {
    sendJSON(["success" => false, "message" => "Property/PG Name is required."], 422);
}

if (empty($address)) {
    sendJSON(["success" => false, "message" => "Property address is required."], 422);
}

// 1. Enforce Plan Limits
$limitCheck = SubscriptionService::checkLimit($orgId, "buildings");
if (!$limitCheck["allowed"]) {
    // Dispatch subscription limit alert notification
    NotificationService::sendToAdmin(
        $orgId,
        null,
        "PLAN_LIMIT_REACHED",
        "Property Limit Reached",
        "Your current plan has reached its maximum number of properties. Upgrade your plan to add more.",
        "plans.php",
        "high"
    );

    sendJSON([
        "success" => false,
        "limit_reached" => true,
        "current" => $limitCheck["current"] ?? 0,
        "max" => $limitCheck["max"] ?? 0,
        "message" => $limitCheck["message"] ?? "Property Limit Reached. Upgrade your plan to add more properties."
    ], 403);
}

$db = getDB();

try {
    $db->beginTransaction();

    $bldCode = "BLD-" . strtoupper(substr(preg_replace("/[^A-Za-z0-9]/", "", $name), 0, 4)) . "-" . random_int(10, 99);

    $stmt = $db->prepare("
        INSERT INTO buildings (
            organization_id, building_code, building_name, property_type,
            address, city, state, pincode, total_floors, curfew_time,
            upi_vpa, upi_payee_name, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \"active\", NOW())
    ");
    $stmt->execute([
        $orgId, $bldCode, $name, $type,
        $address, $city, $state, $pincode, $floors, $curfew,
        $upiVpa, $upiPayee
    ]);
    $newBldId = (int)$db->lastInsertId();

    // Auto-create initial floors
    $stmtFloor = $db->prepare("INSERT INTO floors (organization_id, building_id, floor_number, floor_name) VALUES (?, ?, ?, ?)");
    for ($f = 1; $f <= $floors; $f++) {
        $stmtFloor->execute([$orgId, $newBldId, $f, "Floor {$f}"]);
    }

    // Set new building as active building in session
    TenantContext::setBuildingId($newBldId);

    // Create real operational notification
    NotificationService::sendToAdmin(
        $orgId,
        $newBldId,
        "PROPERTY_CREATED",
        "New Property Added",
        "Property \"{$name}\" ({$bldCode}) was added to your portfolio with {$floors} floor(s).",
        "buildings.php",
        "medium"
    );

    AuditService::log("building_created", "building", $newBldId, null, ["name" => $name, "code" => $bldCode, "floors" => $floors], $orgId, $newBldId);

    $db->commit();

    sendJSON([
        "success" => true,
        "building_id" => $newBldId,
        "building_name" => $name,
        "building_code" => $bldCode,
        "message" => "Property \"{$name}\" created successfully with {$floors} floors!"
    ]);

} catch (\Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    sendJSON(["success" => false, "message" => "Failed to create property: " . $e->getMessage()], 500);
}

