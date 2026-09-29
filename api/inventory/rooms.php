<?php
/**
 * API: Inventory - Rooms CRUD with Multi-Tenant & Sharing Type Bed Auto-Generation
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/RoomBedHelper.php';
require_once __DIR__ . '/../../services/SubscriptionService.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();
$orgId = TenantContext::getOrgId();
$activeBldId = TenantContext::getBuildingId();

if (!$orgId) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: No organization context'], 401);
}

if ($method === 'GET') {
    $floor = $_GET['floor'] ?? null;
    $type = $_GET['room_type'] ?? null;
    $ac = $_GET['ac_type'] ?? null;
    $status = $_GET['status'] ?? null;

    $sql = "
        SELECT r.*, 
               COUNT(b.id) as total_bed_count,
               SUM(CASE WHEN b.status = 'occupied' THEN 1 ELSE 0 END) as occupied_beds,
               SUM(CASE WHEN b.status = 'available' THEN 1 ELSE 0 END) as available_beds
        FROM rooms r
        LEFT JOIN beds b ON r.id = b.room_id
        WHERE r.organization_id = ?
    ";
    $params = [$orgId];

    if ($activeBldId) {
        $sql .= " AND r.building_id = ?";
        $params[] = $activeBldId;
    }
    if (!empty($floor)) {
        $sql .= " AND r.floor = ?";
        $params[] = (int)$floor;
    }
    if (!empty($type)) {
        $sql .= " AND r.room_type = ?";
        $params[] = $type;
    }
    if (!empty($ac)) {
        $sql .= " AND r.ac_type = ?";
        $params[] = $ac;
    }
    if (!empty($status)) {
        $sql .= " AND r.status = ?";
        $params[] = $status;
    }

    $sql .= " GROUP BY r.id ORDER BY r.floor ASC, r.room_number ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rooms = $stmt->fetchAll();

    sendJSON(['success' => true, 'data' => $rooms]);
}

// Write actions require admin / manager
$admin = requireAuth('admin');

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? 'create';

    // --- ACTION: CREATE ROOM & AUTO-GENERATE BEDS ---
    if ($action === 'create') {
        try {
            $roomNumber = trim($input['room_number'] ?? '');
            $floor = (int)($input['floor'] ?? 1);
            $roomType = strtolower(trim($input['room_type'] ?? 'double'));
            $customBeds = isset($input['total_beds']) ? (int)$input['total_beds'] : null;
            $acType = $input['ac_type'] ?? 'AC';
            $baseRent = (float)($input['base_rent'] ?? 6500);
            $amenities = trim($input['amenities'] ?? '');
            $description = trim($input['description'] ?? '');
            $buildingId = !empty($input['building_id']) ? (int)$input['building_id'] : (int)$activeBldId;

            if (empty($roomNumber)) {
                sendJSON(['success' => false, 'message' => 'Room number is required.'], 400);
            }

            // Fallback building if not in session
            if (!$buildingId || $buildingId <= 0) {
                $stmtBld = $db->prepare("SELECT id FROM buildings WHERE organization_id = ? ORDER BY id ASC LIMIT 1");
                $stmtBld->execute([$orgId]);
                $buildingId = (int)$stmtBld->fetchColumn();
                if (!$buildingId) {
                    $orgCode = $db->query("SELECT organization_code FROM organizations WHERE id = {$orgId}")->fetchColumn() ?: 'ORG';
                    $stmtBldIns = $db->prepare("INSERT INTO buildings (organization_code, organization_id, building_code, building_name, status) VALUES (?, ?, 'BLD-01', 'Main Building', 'active')");
                    $stmtBldIns->execute([$orgCode, $orgId]);
                    $buildingId = (int)$db->lastInsertId();
                }
            }

            // Check duplicate within organization and building
            $stmtCheck = $db->prepare("SELECT id FROM rooms WHERE organization_id = ? AND building_id = ? AND room_number = ?");
            $stmtCheck->execute([$orgId, $buildingId, $roomNumber]);
            if ($stmtCheck->rowCount() > 0) {
                sendJSON(['success' => false, 'message' => "Room {$roomNumber} already exists in this property."], 400);
            }

            // Determine beds count dynamically
            $totalBeds = RoomBedHelper::getBedCount($roomType, $customBeds);

            // Check SaaS Bed Limit
            $limitCheck = SubscriptionService::checkLimit($orgId, 'beds', $totalBeds);
            if (!$limitCheck['allowed']) {
                $maxAllowed = $limitCheck['max'] ?? 0;
                sendJSON(['success' => false, 'message' => "Bed quota exceeded. Your current subscription allows {$maxAllowed} beds."], 403);
            }

            // Resolve Floor ID
            $stmtFloor = $db->prepare("SELECT id FROM floors WHERE organization_id = ? AND building_id = ? AND floor_number = ?");
            $stmtFloor->execute([$orgId, $buildingId, $floor]);
            $floorId = $stmtFloor->fetchColumn();
            if (!$floorId) {
                $stmtCreateFloor = $db->prepare("INSERT INTO floors (organization_id, building_id, floor_number, floor_name) VALUES (?, ?, ?, ?)");
                $stmtCreateFloor->execute([$orgId, $buildingId, $floor, "Floor {$floor}"]);
                $floorId = (int)$db->lastInsertId();
            }

            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO rooms (organization_id, building_id, floor_id, room_number, floor, room_type, ac_type, total_beds, base_rent, amenities, description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')
            ");
            $stmt->execute([$orgId, $buildingId, $floorId, $roomNumber, $floor, $roomType, $acType, $totalBeds, $baseRent, $amenities, $description]);
            $roomId = (int)$db->lastInsertId();

            // Auto create beds following naming rules: Single -> 101, Multi -> 102-A, 102-B, etc.
            $bedPrices = $input['bed_prices'] ?? [];
            $bedLabels = RoomBedHelper::getBedLabels($roomNumber, $roomType, $totalBeds);
            foreach ($bedLabels as $idx => $bedNo) {
                $bedRent = $baseRent;
                if (is_array($bedPrices)) {
                    if (isset($bedPrices[$bedNo]) && is_numeric($bedPrices[$bedNo])) {
                        $bedRent = (float)$bedPrices[$bedNo];
                    } elseif (isset($bedPrices[$idx]) && is_numeric($bedPrices[$idx])) {
                        $bedRent = (float)$bedPrices[$idx];
                    }
                }
                if ($bedRent <= 0) $bedRent = $baseRent;

                $stmtBed = $db->prepare("
                    INSERT INTO beds (organization_id, building_id, room_id, bed_number, status, monthly_rent)
                    VALUES (?, ?, ?, ?, 'available', ?)
                ");
                $stmtBed->execute([$orgId, $buildingId, $roomId, $bedNo, $bedRent]);
            }

            // Update building totals
            $db->prepare("
                UPDATE buildings 
                SET total_rooms = (SELECT COUNT(*) FROM rooms WHERE building_id = ?),
                    total_beds = (SELECT COUNT(*) FROM beds WHERE building_id = ?)
                WHERE id = ?
            ")->execute([$buildingId, $buildingId, $buildingId]);

            $db->commit();

            AuditService::log('room_created', 'room', $roomId, null, ['room_number' => $roomNumber, 'beds' => $totalBeds, 'type' => $roomType], $orgId, $buildingId);

            sendJSON([
                'success' => true,
                'message' => "Room {$roomNumber} and {$totalBeds} bed(s) created successfully!",
                'room_id' => $roomId,
                'bed_labels' => $bedLabels
            ]);
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            sendJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    // --- ACTION: EDIT ROOM SHARING TYPE & DETAILS ---
    elseif ($action === 'edit') {
        try {
            $roomId = (int)($input['room_id'] ?? 0);
            if ($roomId <= 0) {
                sendJSON(['success' => false, 'message' => 'Valid Room ID required.'], 400);
            }

            // Fetch current room
            $stmtRoom = $db->prepare("SELECT * FROM rooms WHERE id = ? AND organization_id = ?");
            $stmtRoom->execute([$roomId, $orgId]);
            $room = $stmtRoom->fetch();
            if (!$room) {
                sendJSON(['success' => false, 'message' => 'Room not found or unauthorized.'], 404);
            }

            $roomNumber = trim($room['room_number']);
            $buildingId = (int)$room['building_id'];
            $newFloor = (int)($input['floor'] ?? $room['floor']);
            $newRoomType = strtolower(trim($input['room_type'] ?? $room['room_type']));
            $customBeds = isset($input['total_beds']) ? (int)$input['total_beds'] : null;
            $newAcType = $input['ac_type'] ?? $room['ac_type'];
            $newBaseRent = (float)($input['base_rent'] ?? $room['base_rent']);
            $newAmenities = trim($input['amenities'] ?? $room['amenities']);
            $newDescription = trim($input['description'] ?? $room['description']);

            $newTotalBeds = RoomBedHelper::getBedCount($newRoomType, $customBeds);

            // Fetch existing beds in room
            $stmtBeds = $db->prepare("SELECT * FROM beds WHERE room_id = ? AND organization_id = ? ORDER BY id ASC");
            $stmtBeds->execute([$roomId, $orgId]);
            $currentBeds = $stmtBeds->fetchAll();
            $currentBedCount = count($currentBeds);

            $db->beginTransaction();

            // 1. If downgrading / reducing beds (e.g. Double -> Single or 4 -> 2)
            if ($newTotalBeds < $currentBedCount) {
                // Check if extra beds are occupied
                for ($i = $newTotalBeds; $i < $currentBedCount; $i++) {
                    $b = $currentBeds[$i];
                    if ($b['status'] === 'occupied' || !empty($b['current_tenant_id'])) {
                        $db->rollBack();
                        $targetLabel = RoomBedHelper::getSharingLabel($newRoomType, $newTotalBeds);
                        sendJSON([
                            'success' => false,
                            'message' => "Room cannot be converted to {$targetLabel} because Bed {$b['bed_number']} is occupied."
                        ], 400);
                    }
                }

                // Safe to remove extra empty beds
                for ($i = $newTotalBeds; $i < $currentBedCount; $i++) {
                    $eb = $currentBeds[$i];
                    $db->prepare("DELETE FROM beds WHERE id = ? AND organization_id = ?")->execute([$eb['id'], $orgId]);
                }

                // If converted to single, rename the remaining primary bed to Room Number (e.g. 101)
                if ($newTotalBeds === 1) {
                    $primaryBed = $currentBeds[0];
                    $db->prepare("UPDATE beds SET bed_number = ?, monthly_rent = ? WHERE id = ? AND organization_id = ?")
                       ->execute([$roomNumber, $newBaseRent, $primaryBed['id'], $orgId]);
                } else {
                    // Update remaining bed rents
                    for ($i = 0; $i < $newTotalBeds; $i++) {
                        $db->prepare("UPDATE beds SET monthly_rent = ? WHERE id = ? AND organization_id = ?")
                           ->execute([$newBaseRent, $currentBeds[$i]['id'], $orgId]);
                    }
                }
            }
            // 2. If upgrading / adding beds (e.g. Single -> Double)
            elseif ($newTotalBeds > $currentBedCount) {
                $bedsToAdd = $newTotalBeds - $currentBedCount;
                $limitCheck = SubscriptionService::checkLimit($orgId, 'beds', $bedsToAdd);
                if (!$limitCheck['allowed']) {
                    $db->rollBack();
                    $maxAllowed = $limitCheck['max'] ?? 0;
                    sendJSON(['success' => false, 'message' => "Bed quota exceeded. Your current subscription allows {$maxAllowed} beds."], 403);
                }

                // Generate new bed labels
                $targetLabels = RoomBedHelper::getBedLabels($roomNumber, $newRoomType, $newTotalBeds);

                // Update existing beds to target labels if transitioning from single to multi
                for ($i = 0; $i < $currentBedCount; $i++) {
                    $targetLabel = $targetLabels[$i];
                    $db->prepare("UPDATE beds SET bed_number = ?, monthly_rent = ? WHERE id = ? AND organization_id = ?")
                       ->execute([$targetLabel, $newBaseRent, $currentBeds[$i]['id'], $orgId]);
                }

                // Insert newly added beds
                for ($i = $currentBedCount; $i < $newTotalBeds; $i++) {
                    $targetLabel = $targetLabels[$i];
                    $stmtInsBed = $db->prepare("
                        INSERT INTO beds (organization_id, building_id, room_id, bed_number, status, monthly_rent)
                        VALUES (?, ?, ?, ?, 'available', ?)
                    ");
                    $stmtInsBed->execute([$orgId, $buildingId, $roomId, $targetLabel, $newBaseRent]);
                }
            }
            // 3. Same bed count (e.g. updating rent, amenities, AC)
            else {
                // Ensure proper bed naming
                $targetLabels = RoomBedHelper::getBedLabels($roomNumber, $newRoomType, $newTotalBeds);
                for ($i = 0; $i < $currentBedCount; $i++) {
                    $targetLabel = $targetLabels[$i];
                    $db->prepare("UPDATE beds SET bed_number = ?, monthly_rent = ? WHERE id = ? AND organization_id = ?")
                       ->execute([$targetLabel, $newBaseRent, $currentBeds[$i]['id'], $orgId]);
                }
            }

            // Update room record
            $stmtUpRoom = $db->prepare("
                UPDATE rooms 
                SET floor = ?, room_type = ?, ac_type = ?, total_beds = ?, base_rent = ?, amenities = ?, description = ?
                WHERE id = ? AND organization_id = ?
            ");
            $stmtUpRoom->execute([$newFloor, $newRoomType, $newAcType, $newTotalBeds, $newBaseRent, $newAmenities, $newDescription, $roomId, $orgId]);

            // Update building totals
            $db->prepare("
                UPDATE buildings 
                SET total_rooms = (SELECT COUNT(*) FROM rooms WHERE building_id = ?),
                    total_beds = (SELECT COUNT(*) FROM beds WHERE building_id = ?)
                WHERE id = ?
            ")->execute([$buildingId, $buildingId, $buildingId]);

            $db->commit();

            AuditService::log('room_updated', 'room', $roomId, null, ['room_number' => $roomNumber, 'beds' => $newTotalBeds, 'type' => $newRoomType], $orgId, $buildingId);

            sendJSON([
                'success' => true,
                'message' => "Room {$roomNumber} updated successfully to " . RoomBedHelper::getSharingLabel($newRoomType, $newTotalBeds) . "!"
            ]);
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            sendJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    // --- ACTION: DELETE ROOM (BLOCKED IF OCCUPIED) ---
    elseif ($action === 'delete') {
        $roomId = (int)($input['room_id'] ?? 0);
        if ($roomId <= 0) {
            sendJSON(['success' => false, 'message' => 'Invalid room ID.'], 400);
        }

        // Verify room belongs to org
        $stmtRoom = $db->prepare("SELECT * FROM rooms WHERE id = ? AND organization_id = ?");
        $stmtRoom->execute([$roomId, $orgId]);
        $room = $stmtRoom->fetch();
        if (!$room) {
            sendJSON(['success' => false, 'message' => 'Room not found.'], 404);
        }

        // Check if any beds have active residents
        $stmtCheckOcc = $db->prepare("SELECT COUNT(*) FROM tenant_bookings WHERE room_id = ? AND organization_id = ? AND status = 'active'");
        $stmtCheckOcc->execute([$roomId, $orgId]);
        if ((int)$stmtCheckOcc->fetchColumn() > 0) {
            sendJSON(['success' => false, 'message' => 'Cannot delete room with active residents. Please vacate all beds first.'], 400);
        }

        $stmtBedsOcc = $db->prepare("SELECT COUNT(*) FROM beds WHERE room_id = ? AND organization_id = ? AND status = 'occupied'");
        $stmtBedsOcc->execute([$roomId, $orgId]);
        if ((int)$stmtBedsOcc->fetchColumn() > 0) {
            sendJSON(['success' => false, 'message' => 'Cannot delete room with occupied beds. Please vacate all beds first.'], 400);
        }

        $buildingId = (int)$room['building_id'];

        $db->beginTransaction();
        try {
            $db->prepare("DELETE FROM beds WHERE room_id = ? AND organization_id = ?")->execute([$roomId, $orgId]);
            $db->prepare("DELETE FROM rooms WHERE id = ? AND organization_id = ?")->execute([$roomId, $orgId]);

            // Update building totals
            $db->prepare("
                UPDATE buildings 
                SET total_rooms = (SELECT COUNT(*) FROM rooms WHERE building_id = ?),
                    total_beds = (SELECT COUNT(*) FROM beds WHERE building_id = ?)
                WHERE id = ?
            ")->execute([$buildingId, $buildingId, $buildingId]);

            $db->commit();
            sendJSON(['success' => true, 'message' => "Room {$room['room_number']} and its beds deleted successfully!"]);
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            sendJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }
}
