<?php
/**
 * API: Inventory - Live Occupancy Matrix & Floor Visualizer
 * Scoped by Organization and Active Building.
 */

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$db = getDB();
$orgId = TenantContext::getOrgId();
$activeBldId = TenantContext::getBuildingId();

if (!$orgId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or no organization context']);
    exit();
}

// 1. Overall stats
$stats = [
    'total_rooms' => 0,
    'total_beds' => 0,
    'occupied_beds' => 0,
    'available_beds' => 0,
    'maintenance_beds' => 0,
    'occupancy_rate' => 0
];

$sqlTotal = "
    SELECT 
        COUNT(DISTINCT r.id) as total_rooms,
        COUNT(b.id) as total_beds,
        SUM(CASE WHEN b.status = 'occupied' THEN 1 ELSE 0 END) as occupied_beds,
        SUM(CASE WHEN b.status = 'available' THEN 1 ELSE 0 END) as available_beds,
        SUM(CASE WHEN b.status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_beds
    FROM rooms r
    LEFT JOIN beds b ON r.id = b.room_id
    WHERE r.organization_id = ?
";
$paramsTotal = [$orgId];
if ($activeBldId) {
    $sqlTotal .= " AND r.building_id = ?";
    $paramsTotal[] = $activeBldId;
}

$stmtTotal = $db->prepare($sqlTotal);
$stmtTotal->execute($paramsTotal);
$row = $stmtTotal->fetch();
if ($row) {
    $stats['total_rooms'] = (int)$row['total_rooms'];
    $stats['total_beds'] = (int)$row['total_beds'];
    $stats['occupied_beds'] = (int)$row['occupied_beds'];
    $stats['available_beds'] = (int)$row['available_beds'];
    $stats['maintenance_beds'] = (int)$row['maintenance_beds'];
    $stats['occupancy_rate'] = ($stats['total_beds'] > 0) 
        ? round(($stats['occupied_beds'] / $stats['total_beds']) * 100, 1) 
        : 0;
}

// 2. Fetch all rooms and their beds
$sqlRooms = "SELECT * FROM rooms WHERE organization_id = ?";
$paramsRooms = [$orgId];
if ($activeBldId) {
    $sqlRooms .= " AND building_id = ?";
    $paramsRooms[] = $activeBldId;
}
$sqlRooms .= " ORDER BY floor ASC, room_number ASC";
$stmtRooms = $db->prepare($sqlRooms);
$stmtRooms->execute($paramsRooms);
$allRooms = $stmtRooms->fetchAll();

$sqlBeds = "
    SELECT b.*, u.name as tenant_name, u.phone as tenant_phone, u.avatar, tb.check_in_date, tb.scheduled_checkout_date
    FROM beds b
    LEFT JOIN users u ON b.current_tenant_id = u.id
    LEFT JOIN tenant_bookings tb ON tb.bed_id = b.id AND tb.status = 'active'
    WHERE b.organization_id = ?
";
$paramsBeds = [$orgId];
if ($activeBldId) {
    $sqlBeds .= " AND b.building_id = ?";
    $paramsBeds[] = $activeBldId;
}
$sqlBeds .= " ORDER BY b.bed_number ASC";
$stmtBeds = $db->prepare($sqlBeds);
$stmtBeds->execute($paramsBeds);
$allBeds = $stmtBeds->fetchAll();

require_once __DIR__ . '/../../services/RoomBedHelper.php';

// Group beds by room_id
$bedsByRoom = [];
foreach ($allBeds as $bed) {
    $bedsByRoom[$bed['room_id']][] = $bed;
}

// Group rooms by floor
$matrix = [];
foreach ($allRooms as $room) {
    $floor = $room['floor'];
    $roomBeds = $bedsByRoom[$room['id']] ?? [];
    
    // Sort beds naturally (e.g. 101, or 102-A, 102-B)
    usort($roomBeds, function($a, $b) {
        return strnatcasecmp($a['bed_number'], $b['bed_number']);
    });
    
    $room['beds'] = $roomBeds;
    $room['total_beds'] = (int)$room['total_beds'] > 0 ? (int)$room['total_beds'] : count($roomBeds);
    $room['sharing_label'] = RoomBedHelper::getSharingLabel($room['room_type'], (int)$room['total_beds']);
    $room['price_range'] = RoomBedHelper::getRoomPriceRange($roomBeds, (float)$room['base_rent'], defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '₹');
    
    // Compute room-level stats
    $roomBedCount = count($room['beds']);
    $roomOccupied = 0;
    foreach ($room['beds'] as $b) {
        if ($b['status'] === 'occupied') $roomOccupied++;
    }
    $room['occupied_count'] = $roomOccupied;
    $room['available_count'] = max(0, $roomBedCount - $roomOccupied);
    $room['is_full'] = ($roomBedCount > 0 && $roomOccupied === $roomBedCount);

    $matrix[$floor][] = $room;
}

$floors = [];
foreach ($matrix as $floorNum => $rooms) {
    $floors[] = [
        'floor_number' => $floorNum,
        'rooms' => $rooms
    ];
}

echo json_encode([
    'success' => true,
    'stats' => $stats,
    'floors' => $floors,
    'matrix' => $matrix
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
