<?php
declare(strict_types=1);

/**
 * API: Meals - Opt-in / Opt-out Food Toggle
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/MealService.php';
require_once __DIR__ . '/../../services/EntitlementService.php';

header('Content-Type: application/json; charset=utf-8');

$user = requireAuth();
$orgId = TenantContext::getOrgId() ?: (!empty($user['organization_id']) ? (int)$user['organization_id'] : 1);
EntitlementService::requireAnyFeature($orgId, ['kitchen_management', 'meal_management', 'meal_attendance', 'kitchen_headcount']);


$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get meal attendance for a tenant for a date or week (Strictly scoped to current organization)
    $tenantId = ($user['role'] === 'tenant') ? $user['id'] : (int)($_GET['tenant_id'] ?? $user['id']);
    $startDate = $_GET['start_date'] ?? date('Y-m-d');
    $endDate = $_GET['end_date'] ?? date('Y-m-d', strtotime('+6 days'));

    $stmt = $db->prepare("
        SELECT date, breakfast, lunch, dinner 
        FROM meal_attendance 
        WHERE tenant_id = ? AND organization_id = ? AND date BETWEEN ? AND ? 
        ORDER BY date ASC
    ");
    $stmt->execute([$tenantId, $orgId, $startDate, $endDate]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build a map of existing attendance
    $attendanceMap = [];
    foreach ($records as $r) {
        $attendanceMap[$r['date']] = $r;
    }

    // Prepare full date array with default opt-in (1)
    $result = [];
    $curr = new DateTime($startDate);
    $end = new DateTime($endDate);
    $end->modify('+1 day');

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($curr, $interval, $end);

    $nowTime = date('H:i');
    $todayDate = date('Y-m-d');

    foreach ($period as $dt) {
        $dStr = $dt->format('Y-m-d');
        $item = $attendanceMap[$dStr] ?? [
            'date' => $dStr,
            'breakfast' => 1,
            'lunch' => 1,
            'dinner' => 1
        ];

        // Compute cutoff lock flags
        $isPast = ($dStr < $todayDate);
        $isToday = ($dStr === $todayDate);

        $item['lock_breakfast'] = $isPast || ($isToday && $nowTime > MEAL_CUTOFF_BREAKFAST);
        $item['lock_lunch'] = $isPast || ($isToday && $nowTime > MEAL_CUTOFF_LUNCH);
        $item['lock_dinner'] = $isPast || ($isToday && $nowTime > MEAL_CUTOFF_DINNER);

        $result[] = $item;
    }

    sendJSON([
        'success' => true,
        'cutoffs' => [
            'breakfast' => MEAL_CUTOFF_BREAKFAST,
            'lunch' => MEAL_CUTOFF_LUNCH,
            'dinner' => MEAL_CUTOFF_DINNER
        ],
        'data' => $result
    ]);
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $tenantId = ($user['role'] === 'tenant') ? $user['id'] : (int)($input['tenant_id'] ?? $user['id']);
    $date = trim($input['date'] ?? date('Y-m-d'));

    // Verify tenant belongs to current organization
    $stmtTenant = $db->prepare("SELECT id, building_id FROM users WHERE id = ? AND organization_id = ?");
    $stmtTenant->execute([$tenantId, $orgId]);
    $tRow = $stmtTenant->fetch();
    if (!$tRow) {
        sendJSON(['success' => false, 'message' => 'Resident not found or unauthorized.'], 404);
    }
    $bldId = $tRow['building_id'] ?? TenantContext::getBuildingId();

    // Handle full day update (breakfast, lunch, dinner together)
    if (isset($input['breakfast']) && isset($input['lunch']) && isset($input['dinner'])) {
        $bf = (int)$input['breakfast'];
        $lu = (int)$input['lunch'];
        $dn = (int)$input['dinner'];

        $stmt = $db->prepare("
            INSERT INTO meal_attendance (organization_id, building_id, tenant_id, date, breakfast, lunch, dinner)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE breakfast = VALUES(breakfast), lunch = VALUES(lunch), dinner = VALUES(dinner)
        ");
        $stmt->execute([$orgId, $bldId, $tenantId, $date, $bf, $lu, $dn]);

        sendJSON([
            'success' => true,
            'message' => "Meal attendance updated successfully for " . date('D, d M', strtotime($date)) . "!",
            'date' => $date,
            'breakfast' => $bf,
            'lunch' => $lu,
            'dinner' => $dn
        ]);
    }

    $mealType = $input['meal_type'] ?? ''; // 'breakfast', 'lunch', 'dinner'
    $status = (int)($input['status'] ?? 1); // 1 = attending, 0 = skipped

    if (!in_array($mealType, ['breakfast', 'lunch', 'dinner'])) {
        sendJSON(['success' => false, 'message' => 'Invalid meal type. Must be breakfast, lunch, or dinner.'], 400);
    }

    // Cutoff validation only enforced for tenants
    if ($user['role'] === 'tenant') {
        $today = date('Y-m-d');
        $now = date('H:i');

        if ($date < $today) {
            sendJSON(['success' => false, 'message' => 'Cannot modify past meal attendance.'], 400);
        }

        if ($date === $today) {
            $cutoff = ($mealType === 'breakfast') ? MEAL_CUTOFF_BREAKFAST : (($mealType === 'lunch') ? MEAL_CUTOFF_LUNCH : MEAL_CUTOFF_DINNER);
            if ($now > $cutoff) {
                sendJSON(['success' => false, 'message' => "Cutoff time for today's {$mealType} ({$cutoff}) has passed."], 400);
            }
        }
    }

    // Insert or update record
    $stmt = $db->prepare("
        INSERT INTO meal_attendance (organization_id, building_id, tenant_id, date, {$mealType})
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE {$mealType} = VALUES({$mealType})
    ");
    $stmt->execute([$orgId, $bldId, $tenantId, $date, $status]);

    $actionText = ($status === 1) ? 'opted-in for' : 'opted-out of';

    sendJSON([
        'success' => true,
        'message' => "Successfully {$actionText} " . ucfirst($mealType) . " on " . date('D, d M', strtotime($date)) . "!",
        'date' => $date,
        'meal_type' => $mealType,
        'status' => $status
    ]);
}
