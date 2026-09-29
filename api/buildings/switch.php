<?php
declare(strict_types=1);

/**
 * API: Properties / Buildings - Switch Active Property Context
 */

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../services/TenantContext.php";

header("Content-Type: application/json; charset=utf-8");

$admin = requireAuth("admin");
$orgId = TenantContext::getOrgId();

if (!$orgId) {
    sendJSON(["success" => false, "message" => "Unauthorized"], 401);
}

$input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
$buildingId = (int)($input["building_id"] ?? $_GET["building_id"] ?? 0);

if ($buildingId <= 0) {
    sendJSON(["success" => false, "message" => "Invalid building ID"], 422);
}

$success = TenantContext::setBuildingId($buildingId);

if ($success) {
    $bldInfo = TenantContext::getBuildingInfo();
    sendJSON([
        "success" => true,
        "building_id" => $buildingId,
        "building_name" => $bldInfo["building_name"] ?? "Property",
        "message" => "Active property switched successfully."
    ]);
} else {
    sendJSON(["success" => false, "message" => "Unauthorized property selection."], 403);
}

