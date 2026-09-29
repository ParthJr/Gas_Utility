<?php
declare(strict_types=1);

/**
 * API: Meals - Kitchen Headcount Summary
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/MealService.php';
require_once __DIR__ . '/../../services/EntitlementService.php';

header('Content-Type: application/json; charset=utf-8');

$authUser = getAuthUser();
if (!$authUser) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Please log in.'], 401);
}

$orgId = TenantContext::getOrgId() ?: (!empty($authUser['organization_id']) ? (int)$authUser['organization_id'] : 1);
if (!$orgId) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Missing organization context.'], 401);
}

EntitlementService::requireAnyFeature($orgId, ['kitchen_management', 'meal_management', 'meal_attendance', 'kitchen_headcount']);

$db = getDB();
$activeBldId = TenantContext::getBuildingId() ?: (!empty($authUser['building_id']) ? (int)$authUser['building_id'] : null);
$targetDate = $_GET['date'] ?? date('Y-m-d');


// Total active tenants in the organization / building
$sqlTotal = "SELECT COUNT(*) FROM tenant_bookings WHERE organization_id = ? AND status = 'active'";
$paramsTotal = [$orgId];
if ($activeBldId) {
    $sqlTotal .= " AND building_id = ?";
    $paramsTotal[] = $activeBldId;
}
$stmtTotal = $db->prepare($sqlTotal);
$stmtTotal->execute($paramsTotal);
$totalActiveTenants = (int)$stmtTotal->fetchColumn();

// Fetch opted-out count for this date
$sqlAttendance = "
    SELECT 
        SUM(CASE WHEN breakfast = 1 THEN 1 ELSE 0 END) as breakfast_count,
        SUM(CASE WHEN lunch = 1 THEN 1 ELSE 0 END) as lunch_count,
        SUM(CASE WHEN dinner = 1 THEN 1 ELSE 0 END) as dinner_count,
        COUNT(id) as total_responses
    FROM meal_attendance 
    WHERE organization_id = ? AND date = ?
";
$paramsAtt = [$orgId, $targetDate];
if ($activeBldId) {
    $sqlAttendance .= " AND building_id = ?";
    $paramsAtt[] = $activeBldId;
}
$stmtAttendance = $db->prepare($sqlAttendance);
$stmtAttendance->execute($paramsAtt);
$stats = $stmtAttendance->fetch();

// By default, if a tenant has NOT explicitly marked 0, they are counted as attending (opt-in by default)
$sqlOptOuts = "
    SELECT 
        SUM(CASE WHEN breakfast = 0 THEN 1 ELSE 0 END) as breakfast_skips,
        SUM(CASE WHEN lunch = 0 THEN 1 ELSE 0 END) as lunch_skips,
        SUM(CASE WHEN dinner = 0 THEN 1 ELSE 0 END) as dinner_skips
    FROM meal_attendance 
    WHERE organization_id = ? AND date = ?
";
$paramsOpt = [$orgId, $targetDate];
if ($activeBldId) {
    $sqlOptOuts .= " AND building_id = ?";
    $paramsOpt[] = $activeBldId;
}
$stmtOptOuts = $db->prepare($sqlOptOuts);
$stmtOptOuts->execute($paramsOpt);
$skips = $stmtOptOuts->fetch();

$bSkips = (int)($skips['breakfast_skips'] ?? 0);
$lSkips = (int)($skips['lunch_skips'] ?? 0);
$dSkips = (int)($skips['dinner_skips'] ?? 0);

$menu = MealService::getMenu($orgId, $activeBldId, $targetDate);

$bCutoff = $menu['breakfast_cutoff'] ?? (defined('MEAL_CUTOFF_BREAKFAST') ? MEAL_CUTOFF_BREAKFAST : '07:30');
$lCutoff = $menu['lunch_cutoff'] ?? (defined('MEAL_CUTOFF_LUNCH') ? MEAL_CUTOFF_LUNCH : '11:30');
$dCutoff = $menu['dinner_cutoff'] ?? (defined('MEAL_CUTOFF_DINNER') ? MEAL_CUTOFF_DINNER : '18:30');

$headcounts = [
    'date' => $targetDate,
    'total_residents' => $totalActiveTenants,
    'breakfast' => [
        'attending' => max(0, $totalActiveTenants - $bSkips),
        'skipping' => $bSkips,
        'cutoff_time' => $bCutoff,
        'menu' => $menu['breakfast_menu'] ?? '',
        'is_set' => !empty(trim((string)($menu['breakfast_menu'] ?? '')))
    ],
    'lunch' => [
        'attending' => max(0, $totalActiveTenants - $lSkips),
        'skipping' => $lSkips,
        'cutoff_time' => $lCutoff,
        'menu' => $menu['lunch_menu'] ?? '',
        'is_set' => !empty(trim((string)($menu['lunch_menu'] ?? '')))
    ],
    'dinner' => [
        'attending' => max(0, $totalActiveTenants - $dSkips),
        'skipping' => $dSkips,
        'cutoff_time' => $dCutoff,
        'menu' => $menu['dinner_menu'] ?? '',
        'is_set' => !empty(trim((string)($menu['dinner_menu'] ?? '')))
    ]
];

// List of tenants skipping meals today for kitchen reference
$sqlSkipList = "
    SELECT ma.*, u.name as tenant_name, u.phone, r.room_number, b.bed_number
    FROM meal_attendance ma
    JOIN users u ON ma.tenant_id = u.id
    JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.status = 'active'
    JOIN rooms r ON tb.room_id = r.id
    JOIN beds b ON tb.bed_id = b.id
    WHERE ma.organization_id = ? AND ma.date = ? AND (ma.breakfast = 0 OR ma.lunch = 0 OR ma.dinner = 0)
";
$paramsSkip = [$orgId, $targetDate];
if ($activeBldId) {
    $sqlSkipList .= " AND ma.building_id = ?";
    $paramsSkip[] = $activeBldId;
}
$stmtSkipList = $db->prepare($sqlSkipList);
$stmtSkipList->execute($paramsSkip);
$skipDetails = $stmtSkipList->fetchAll();

// List of all active residents with attendance for date
$sqlAll = "
    SELECT u.id as tenant_id, u.name as tenant_name, u.phone, r.room_number, b.bed_number,
           COALESCE(ma.breakfast, 1) as breakfast,
           COALESCE(ma.lunch, 1) as lunch,
           COALESCE(ma.dinner, 1) as dinner,
           ma.id as attendance_id
    FROM tenant_bookings tb
    JOIN users u ON tb.tenant_id = u.id
    JOIN rooms r ON tb.room_id = r.id
    JOIN beds b ON tb.bed_id = b.id
    LEFT JOIN meal_attendance ma ON ma.tenant_id = u.id AND ma.date = ? AND ma.organization_id = ?
    WHERE tb.organization_id = ? AND tb.status = 'active'
";
$paramsAll = [$targetDate, $orgId, $orgId];
if ($activeBldId) {
    $sqlAll .= " AND tb.building_id = ?";
    $paramsAll[] = $activeBldId;
}
$sqlAll .= " ORDER BY r.room_number ASC, b.bed_number ASC";
$stmtAll = $db->prepare($sqlAll);
$stmtAll->execute($paramsAll);
$residents = $stmtAll->fetchAll();

sendJSON([
    'success' => true,
    'headcounts' => $headcounts,
    'menu' => $menu,
    'skip_list' => $skipDetails,
    'residents' => $residents
]);
