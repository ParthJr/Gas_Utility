<?php
/**
 * API: Inventory - Beds Allocation, Vacate, Transfer & Individual Bed Pricing with Strict 1:1 Active Resident-Bed Integrity
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/RoomBedHelper.php';
require_once __DIR__ . '/../../services/BillingEngine.php';
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
    $roomId = $_GET['room_id'] ?? null;
    $bedId = $_GET['bed_id'] ?? null;
    $status = $_GET['status'] ?? null;
    $action = $_GET['action'] ?? null;
    $includeHistory = !empty($_GET['include_history']);

    // Helper: Return residents eligible for bed allocation (with active allocation info)
    if ($action === 'eligible_residents') {
        $stmtEligible = $db->prepare("
            SELECT u.id, u.name, u.phone, u.email,
                   tb.id as active_booking_id, tb.bed_id as active_bed_id, tb.monthly_rent as active_rent,
                   b.bed_number as active_bed_number, r.room_number as active_room_number
            FROM users u
            LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.organization_id = u.organization_id AND tb.status = 'active'
            LEFT JOIN beds b ON tb.bed_id = b.id
            LEFT JOIN rooms r ON tb.room_id = r.id
            WHERE u.organization_id = ? AND u.role = 'tenant' AND u.status = 'active'
            ORDER BY (tb.id IS NULL) DESC, u.name ASC
        ");
        $stmtEligible->execute([$orgId]);
        $residents = $stmtEligible->fetchAll(PDO::FETCH_ASSOC);

        sendJSON(['success' => true, 'data' => $residents]);
    }

    if ($bedId) {
        $stmtBed = $db->prepare("
            SELECT b.*, r.room_number, r.room_type, r.ac_type, r.floor, r.base_rent as room_base_rent,
                   u.name as tenant_name, u.phone as tenant_phone, u.email as tenant_email,
                   tb.monthly_rent as booking_rent, tb.check_in_date
            FROM beds b
            JOIN rooms r ON b.room_id = r.id
            LEFT JOIN users u ON b.current_tenant_id = u.id
            LEFT JOIN tenant_bookings tb ON tb.bed_id = b.id AND tb.status = 'active'
            WHERE b.id = ? AND b.organization_id = ?
        ");
        $stmtBed->execute([(int)$bedId, $orgId]);
        $bed = $stmtBed->fetch();

        if (!$bed) {
            sendJSON(['success' => false, 'message' => 'Bed not found'], 404);
        }

        $history = [];
        if ($includeHistory) {
            $stmtHist = $db->prepare("
                SELECT bph.*, u.name as changed_by_name
                FROM bed_price_history bph
                LEFT JOIN users u ON bph.changed_by = u.id
                WHERE bph.bed_id = ? AND bph.organization_id = ?
                ORDER BY bph.created_at DESC
            ");
            $stmtHist->execute([(int)$bedId, $orgId]);
            $history = $stmtHist->fetchAll();
        }

        sendJSON(['success' => true, 'data' => $bed, 'price_history' => $history]);
    }

    $sql = "
        SELECT b.*, r.room_number, r.room_type, r.ac_type, r.floor, r.base_rent as room_base_rent,
               u.name as tenant_name, u.phone as tenant_phone, u.email as tenant_email,
               tb.monthly_rent as booking_rent
        FROM beds b
        JOIN rooms r ON b.room_id = r.id
        LEFT JOIN users u ON b.current_tenant_id = u.id
        LEFT JOIN tenant_bookings tb ON tb.bed_id = b.id AND tb.status = 'active'
        WHERE b.organization_id = ?
    ";
    $params = [$orgId];

    if ($activeBldId) {
        $sql .= " AND b.building_id = ?";
        $params[] = $activeBldId;
    }
    if (!empty($roomId)) {
        $sql .= " AND b.room_id = ?";
        $params[] = (int)$roomId;
    }
    if (!empty($status)) {
        $sql .= " AND b.status = ?";
        $params[] = $status;
    }

    $sql .= " ORDER BY r.floor ASC, r.room_number ASC, b.bed_number ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $beds = $stmt->fetchAll();

    sendJSON(['success' => true, 'data' => $beds]);
}

$admin = requireAuth('admin');

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? 'assign';

    // ==========================================
    // ACTION: ASSIGN BED TO RESIDENT
    // Strict 1:1 Active Resident-Bed Integrity
    // ==========================================
    if ($action === 'assign') {
        $bedId = (int)($input['bed_id'] ?? 0);
        $tenantId = (int)($input['tenant_id'] ?? 0);
        $customDeposit = isset($input['deposit_amount']) && (float)$input['deposit_amount'] > 0 ? (float)$input['deposit_amount'] : null;
        $checkInDate = !empty($input['check_in_date']) ? trim($input['check_in_date']) : date('Y-m-d');

        if ($bedId <= 0 || $tenantId <= 0) {
            sendJSON(['success' => false, 'message' => 'Valid Bed ID and Tenant ID are required.'], 400);
        }

        $db->beginTransaction();

        try {
            // 1. Verify tenant exists in current organization and lock user
            $stmtCheckTenant = $db->prepare("
                SELECT id, name FROM users 
                WHERE id = ? AND organization_id = ? AND role = 'tenant' AND status = 'active'
                FOR UPDATE
            ");
            $stmtCheckTenant->execute([$tenantId, $orgId]);
            $tenant = $stmtCheckTenant->fetch();
            if (!$tenant) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => 'Resident not found or does not belong to your organization.'], 404);
            }

            // 2. Strict 1:1 Resident Active Allocation Check (Lock active bookings)
            $stmtExisting = $db->prepare("
                SELECT tb.id, tb.bed_id, b.bed_number, r.room_number 
                FROM tenant_bookings tb
                JOIN beds b ON tb.bed_id = b.id
                JOIN rooms r ON tb.room_id = r.id
                WHERE tb.tenant_id = ? AND tb.organization_id = ? AND tb.status = 'active'
                FOR UPDATE
            ");
            $stmtExisting->execute([$tenantId, $orgId]);
            $existingBooking = $stmtExisting->fetch();

            if ($existingBooking) {
                $db->rollBack();
                sendJSON([
                    'success' => false,
                    'message' => "Resident '{$tenant['name']}' is already actively allocated to Room {$existingBooking['room_number']} (Bed {$existingBooking['bed_number']}). A resident can only have ONE active bed. Please vacate or transfer them first."
                ], 400);
            }

            // 3. Strict 1:1 Bed Availability Check (Lock target bed)
            $stmtBed = $db->prepare("
                SELECT b.*, r.base_rent, r.id as room_id, r.room_number, r.building_id, bld.invoice_prefix, bld.maintenance_charge
                FROM beds b 
                JOIN rooms r ON b.room_id = r.id 
                JOIN buildings bld ON r.building_id = bld.id
                WHERE b.id = ? AND b.organization_id = ?
                FOR UPDATE
            ");
            $stmtBed->execute([$bedId, $orgId]);
            $bed = $stmtBed->fetch();

            if (!$bed) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => 'Bed not found or unauthorized.'], 404);
            }

            // Check if bed is already occupied or has an active booking
            $stmtActiveOnBed = $db->prepare("SELECT id FROM tenant_bookings WHERE bed_id = ? AND organization_id = ? AND status = 'active' FOR UPDATE");
            $stmtActiveOnBed->execute([$bedId, $orgId]);
            if ($bed['status'] === 'occupied' || $bed['current_tenant_id'] !== null || $stmtActiveOnBed->fetch()) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => "Bed {$bed['bed_number']} in Room {$bed['room_number']} is already occupied!"], 400);
            }

            // 4. Resolve Effective Rent (Resident Negotiated Rent -> Bed Custom Rent -> Room Base Rent)
            $customInputRent = isset($input['monthly_rent']) && (float)$input['monthly_rent'] > 0 ? (float)$input['monthly_rent'] : null;
            $effectiveRent = RoomBedHelper::getEffectivePrice($customInputRent, (float)$bed['monthly_rent'], (float)$bed['base_rent']);
            $depMonths = defined('DEFAULT_SECURITY_DEPOSIT_MONTHS') ? DEFAULT_SECURITY_DEPOSIT_MONTHS : 2;
            $deposit = ($customDeposit !== null && $customDeposit > 0) ? $customDeposit : round($effectiveRent * $depMonths, 2);

            $bldId = (int)$bed['building_id'];

            // 5. Update bed status
            $stmtUpBed = $db->prepare("UPDATE beds SET status = 'occupied', current_tenant_id = ? WHERE id = ? AND organization_id = ?");
            $stmtUpBed->execute([$tenantId, $bedId, $orgId]);

            // Update user's active building
            $db->prepare("UPDATE users SET building_id = ? WHERE id = ? AND organization_id = ?")->execute([$bldId, $tenantId, $orgId]);

            // 6. Create booking record
            $stmtBook = $db->prepare("
                INSERT INTO tenant_bookings (organization_id, building_id, tenant_id, room_id, bed_id, check_in_date, deposit_amount, deposit_status, monthly_rent, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', ?, 'active')
            ");
            $stmtBook->execute([$orgId, $bldId, $tenantId, $bed['room_id'], $bedId, $checkInDate, $deposit, $effectiveRent]);

            // 7. Update room status if all beds are occupied
            $stmtCheckAll = $db->prepare("SELECT COUNT(*) as unocc FROM beds WHERE room_id = ? AND organization_id = ? AND status != 'occupied'");
            $stmtCheckAll->execute([$bed['room_id'], $orgId]);
            $unocc = (int)$stmtCheckAll->fetchColumn();
            if ($unocc === 0) {
                $db->prepare("UPDATE rooms SET status = 'full' WHERE id = ? AND organization_id = ?")->execute([$bed['room_id'], $orgId]);
            }

            // 8. Auto-generate invoice for current month for the resident using effective bed rent
            $currentMonth = date('Y-m');
            $stmtInvCheck = $db->prepare("SELECT id FROM invoices WHERE tenant_id = ? AND billing_month = ?");
            $stmtInvCheck->execute([$tenantId, $currentMonth]);
            if ($stmtInvCheck->rowCount() === 0) {
                $prefix = $bed['invoice_prefix'] ?: 'INV';
                $invNo = sprintf("%s-%s-%03d", $prefix, str_replace('-', '', $currentMonth), $tenantId);
                
                $stmtEb = $db->prepare("SELECT split_amount_per_tenant, units_consumed FROM eb_readings WHERE room_id = ? AND billing_month = ?");
                $stmtEb->execute([$bed['room_id'], $currentMonth]);
                $eb = $stmtEb->fetch();
                $ebAmount = $eb ? (float)$eb['split_amount_per_tenant'] : 0.00;
                $ebUnits = $eb ? (float)$eb['units_consumed'] : 0.00;
                $maint = (float)($bed['maintenance_charge'] ?? 300.00);

                $calculated = BillingEngine::calculateInvoice([
                    'rent_amount' => $effectiveRent,
                    'eb_amount' => $ebAmount,
                    'eb_units' => $ebUnits,
                    'maintenance_amount' => $maint
                ]);

                $dueD = date('Y-m-05');

                $stmtInsInv = $db->prepare("
                    INSERT INTO invoices 
                    (organization_id, building_id, invoice_no, tenant_id, room_id, bed_id, billing_month, rent_amount, eb_units, eb_amount, maintenance_amount, late_fee, discount_amount, subtotal_amount, total_amount, paid_amount, due_date, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 0.00, ?, ?, 0.00, ?, 'pending')
                ");
                $stmtInsInv->execute([
                    $orgId, $bldId, $invNo, $tenantId, $bed['room_id'], $bedId, $currentMonth,
                    $calculated['rent_amount'], $calculated['eb_units'], $calculated['eb_amount'], $calculated['maintenance_amount'],
                    $calculated['subtotal_amount'], $calculated['total_amount'], $dueD
                ]);
            }

            AuditService::log('bed_assigned', 'bed', $bedId, null, ['tenant_id' => $tenantId, 'rent' => $effectiveRent], $orgId, $bldId);

            $db->commit();

            sendJSON([
                'success' => true,
                'message' => "Bed {$bed['bed_number']} assigned to {$tenant['name']} at " . CURRENCY_SYMBOL . number_format($effectiveRent) . "/mo successfully!"
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            // Handle duplicate constraint violation specifically
            if (strpos($e->getMessage(), 'uq_tb_active_tenant') !== false) {
                sendJSON(['success' => false, 'message' => 'Integrity Error: Resident already has an active bed allocation.'], 400);
            } elseif (strpos($e->getMessage(), 'uq_tb_active_bed') !== false) {
                sendJSON(['success' => false, 'message' => 'Integrity Error: This bed is already allocated to another active resident.'], 400);
            }
            sendJSON(['success' => false, 'message' => 'Error assigning bed: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // ACTION: RESIDENT TRANSFER (BED TO BED)
    // Closes old allocation, frees old bed, and assigns new bed atomically
    // ==========================================
    elseif ($action === 'transfer') {
        $tenantId = (int)($input['tenant_id'] ?? 0);
        $toBedId = (int)($input['to_bed_id'] ?? 0);
        $customRent = isset($input['monthly_rent']) && (float)$input['monthly_rent'] > 0 ? (float)$input['monthly_rent'] : null;
        $transferDate = !empty($input['transfer_date']) ? trim($input['transfer_date']) : date('Y-m-d');

        if ($tenantId <= 0 || $toBedId <= 0) {
            sendJSON(['success' => false, 'message' => 'Valid Tenant ID and Target Bed ID are required for transfer.'], 400);
        }

        $db->beginTransaction();

        try {
            // 1. Fetch current active booking for resident
            $stmtCurrent = $db->prepare("
                SELECT tb.*, b.id as from_bed_id, b.bed_number as from_bed_number,
                       r.id as from_room_id, r.room_number as from_room_number,
                       u.name as tenant_name
                FROM tenant_bookings tb
                JOIN users u ON tb.tenant_id = u.id
                JOIN beds b ON tb.bed_id = b.id
                JOIN rooms r ON tb.room_id = r.id
                WHERE tb.tenant_id = ? AND tb.organization_id = ? AND tb.status = 'active'
                FOR UPDATE
            ");
            $stmtCurrent->execute([$tenantId, $orgId]);
            $currentBooking = $stmtCurrent->fetch();

            if (!$currentBooking) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => 'Resident has no active bed allocation to transfer from.'], 404);
            }

            $fromBedId = (int)$currentBooking['from_bed_id'];
            $fromRoomId = (int)$currentBooking['from_room_id'];

            if ($fromBedId === $toBedId) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => 'Resident is already allocated to this exact bed.'], 400);
            }

            // 2. Fetch and lock target bed
            $stmtTarget = $db->prepare("
                SELECT b.*, r.base_rent, r.id as room_id, r.room_number as to_room_number,
                       r.building_id as to_building_id, bld.invoice_prefix, bld.maintenance_charge
                FROM beds b 
                JOIN rooms r ON b.room_id = r.id 
                JOIN buildings bld ON r.building_id = bld.id
                WHERE b.id = ? AND b.organization_id = ?
                FOR UPDATE
            ");
            $stmtTarget->execute([$toBedId, $orgId]);
            $targetBed = $stmtTarget->fetch();

            if (!$targetBed) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => 'Target bed not found or unauthorized.'], 404);
            }

            // Check if target bed is occupied
            $stmtActiveOnTarget = $db->prepare("SELECT id FROM tenant_bookings WHERE bed_id = ? AND organization_id = ? AND status = 'active' FOR UPDATE");
            $stmtActiveOnTarget->execute([$toBedId, $orgId]);
            if ($targetBed['status'] === 'occupied' || $targetBed['current_tenant_id'] !== null || $stmtActiveOnTarget->fetch()) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => "Target Bed {$targetBed['bed_number']} (Room {$targetBed['to_room_number']}) is already occupied!"], 400);
            }

            // 3. Vacate Old Bed
            $db->prepare("
                UPDATE tenant_bookings 
                SET status = 'vacated', check_out_date = ? 
                WHERE id = ? AND organization_id = ?
            ")->execute([$transferDate, $currentBooking['id'], $orgId]);

            $db->prepare("UPDATE beds SET status = 'available', current_tenant_id = NULL WHERE id = ? AND organization_id = ?")->execute([$fromBedId, $orgId]);
            $db->prepare("UPDATE rooms SET status = 'available' WHERE id = ? AND organization_id = ?")->execute([$fromRoomId, $orgId]);

            // 4. Assign New Bed
            $toBldId = (int)$targetBed['to_building_id'];
            $newRent = RoomBedHelper::getEffectivePrice($customRent, (float)$targetBed['monthly_rent'], (float)$targetBed['base_rent']);
            $deposit = (float)$currentBooking['deposit_amount'];

            $db->prepare("UPDATE beds SET status = 'occupied', current_tenant_id = ? WHERE id = ? AND organization_id = ?")->execute([$tenantId, $toBedId, $orgId]);
            $db->prepare("UPDATE users SET building_id = ? WHERE id = ? AND organization_id = ?")->execute([$toBldId, $tenantId, $orgId]);

            $stmtNewBook = $db->prepare("
                INSERT INTO tenant_bookings (organization_id, building_id, tenant_id, room_id, bed_id, check_in_date, deposit_amount, deposit_status, monthly_rent, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', ?, 'active')
            ");
            $stmtNewBook->execute([$orgId, $toBldId, $tenantId, $targetBed['room_id'], $toBedId, $transferDate, $deposit, $newRent]);

            // 5. Update Target Room status if full
            $stmtCheckTargetAll = $db->prepare("SELECT COUNT(*) as unocc FROM beds WHERE room_id = ? AND organization_id = ? AND status != 'occupied'");
            $stmtCheckTargetAll->execute([$targetBed['room_id'], $orgId]);
            if ((int)$stmtCheckTargetAll->fetchColumn() === 0) {
                $db->prepare("UPDATE rooms SET status = 'full' WHERE id = ? AND organization_id = ?")->execute([$targetBed['room_id'], $orgId]);
            }

            AuditService::log('bed_transferred', 'bed', $toBedId, [
                'from_bed' => $currentBooking['from_bed_number'],
                'from_room' => $currentBooking['from_room_number'],
                'old_rent' => $currentBooking['monthly_rent']
            ], [
                'to_bed' => $targetBed['bed_number'],
                'to_room' => $targetBed['to_room_number'],
                'new_rent' => $newRent,
                'transfer_date' => $transferDate
            ], $orgId, $toBldId);

            $db->commit();

            sendJSON([
                'success' => true,
                'message' => "Transferred {$currentBooking['tenant_name']} from Bed {$currentBooking['from_bed_number']} to Bed {$targetBed['bed_number']} successfully!",
                'new_rent' => $newRent
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            sendJSON(['success' => false, 'message' => 'Error transferring resident: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // ACTION: VACATE BED / CHECKOUT
    // ==========================================
    elseif ($action === 'vacate') {
        $bedId = (int)($input['bed_id'] ?? 0);
        $checkoutDate = !empty($input['checkout_date']) ? trim($input['checkout_date']) : date('Y-m-d');

        if ($bedId <= 0) {
            sendJSON(['success' => false, 'message' => 'Valid Bed ID required.'], 400);
        }

        $db->beginTransaction();

        try {
            $stmtBed = $db->prepare("SELECT b.*, r.id as room_id, r.building_id FROM beds b JOIN rooms r ON b.room_id = r.id WHERE b.id = ? AND b.organization_id = ? FOR UPDATE");
            $stmtBed->execute([$bedId, $orgId]);
            $bed = $stmtBed->fetch();

            if (!$bed) {
                $db->rollBack();
                sendJSON(['success' => false, 'message' => 'Bed not found.'], 404);
            }

            $tenantId = $bed['current_tenant_id'];

            // Mark booking as vacated
            if ($tenantId) {
                $stmtCloseBooking = $db->prepare("
                    UPDATE tenant_bookings 
                    SET status = 'vacated', check_out_date = ? 
                    WHERE tenant_id = ? AND bed_id = ? AND organization_id = ? AND status = 'active'
                ");
                $stmtCloseBooking->execute([$checkoutDate, $tenantId, $bedId, $orgId]);
            } else {
                // If current_tenant_id was NULL, check if any active booking exists on bed
                $db->prepare("
                    UPDATE tenant_bookings 
                    SET status = 'vacated', check_out_date = ? 
                    WHERE bed_id = ? AND organization_id = ? AND status = 'active'
                ")->execute([$checkoutDate, $bedId, $orgId]);
            }

            // Free bed
            $stmtFree = $db->prepare("UPDATE beds SET status = 'available', current_tenant_id = NULL WHERE id = ? AND organization_id = ?");
            $stmtFree->execute([$bedId, $orgId]);

            // Update room status back to available
            $db->prepare("UPDATE rooms SET status = 'available' WHERE id = ? AND organization_id = ?")->execute([$bed['room_id'], $orgId]);

            AuditService::log('bed_vacated', 'bed', $bedId, null, ['vacated_tenant_id' => $tenantId, 'checkout_date' => $checkoutDate], $orgId, (int)$bed['building_id']);

            $db->commit();

            sendJSON(['success' => true, 'message' => "Bed {$bed['bed_number']} checked out / marked as vacated and now available!"]);
        } catch (Exception $e) {
            $db->rollBack();
            sendJSON(['success' => false, 'message' => 'Error vacating bed: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // ACTION: UPDATE INDIVIDUAL BED PRICE
    // ==========================================
    elseif ($action === 'update_price') {
        $bedId = (int)($input['bed_id'] ?? 0);
        $newPrice = (float)($input['monthly_rent'] ?? 0);
        $effectiveFrom = !empty($input['effective_from']) ? trim($input['effective_from']) : date('Y-m-d');
        $updateActiveBooking = !empty($input['update_active_booking']);

        if ($bedId <= 0 || $newPrice <= 0) {
            sendJSON(['success' => false, 'message' => 'Valid Bed ID and positive Monthly Rent are required.'], 400);
        }

        // Fetch Bed
        $stmtBed = $db->prepare("SELECT * FROM beds WHERE id = ? AND organization_id = ? FOR UPDATE");
        $stmtBed->execute([$bedId, $orgId]);
        $bed = $stmtBed->fetch();

        if (!$bed) {
            sendJSON(['success' => false, 'message' => 'Bed not found or unauthorized.'], 404);
        }

        $oldPrice = (float)$bed['monthly_rent'];
        $userId = (int)($_SESSION['user_id'] ?? 1);
        $bldId = (int)$bed['building_id'];
        $roomId = (int)$bed['room_id'];

        $db->beginTransaction();

        try {
            // Update Bed Monthly Rent
            $db->prepare("UPDATE beds SET monthly_rent = ? WHERE id = ? AND organization_id = ?")->execute([$newPrice, $bedId, $orgId]);

            // Record in Bed Price History
            $stmtHist = $db->prepare("
                INSERT INTO bed_price_history (organization_id, building_id, room_id, bed_id, old_price, new_price, effective_from, changed_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmtHist->execute([$orgId, $bldId, $roomId, $bedId, $oldPrice, $newPrice, $effectiveFrom, $userId]);

            // Update active resident contract price if requested
            if ($updateActiveBooking && !empty($bed['current_tenant_id'])) {
                $db->prepare("
                    UPDATE tenant_bookings 
                    SET monthly_rent = ? 
                    WHERE bed_id = ? AND tenant_id = ? AND organization_id = ? AND status = 'active'
                ")->execute([$newPrice, $bedId, $bed['current_tenant_id'], $orgId]);
            }

            AuditService::log('bed_price_changed', 'bed', $bedId, ['monthly_rent' => $oldPrice], ['monthly_rent' => $newPrice, 'effective_from' => $effectiveFrom], $orgId, $bldId);

            $db->commit();

            sendJSON([
                'success' => true,
                'message' => "Bed {$bed['bed_number']} monthly rent updated from " . CURRENCY_SYMBOL . number_format($oldPrice) . " to " . CURRENCY_SYMBOL . number_format($newPrice) . "/month!",
                'bed_id' => $bedId,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'effective_from' => $effectiveFrom
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            sendJSON(['success' => false, 'message' => 'Error updating bed price: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // ACTION: SCHEDULE CHECKOUT DATE
    // ==========================================
    elseif ($action === 'schedule_checkout') {
        $bedId = (int)($input['bed_id'] ?? 0);
        $scheduledDate = !empty($input['checkout_date']) ? trim($input['checkout_date']) : null;
        $notes = !empty($input['notes']) ? trim($input['notes']) : null;

        if ($bedId <= 0 || !$scheduledDate) {
            sendJSON(['success' => false, 'message' => 'Bed ID and Scheduled Checkout Date are required.'], 400);
        }

        $stmtBed = $db->prepare("SELECT * FROM beds WHERE id = ? AND organization_id = ?");
        $stmtBed->execute([$bedId, $orgId]);
        $bed = $stmtBed->fetch();

        if (!$bed) {
            sendJSON(['success' => false, 'message' => 'Bed not found.'], 404);
        }

        $stmtSched = $db->prepare("
            UPDATE tenant_bookings 
            SET scheduled_checkout_date = ?, checkout_notice_notes = ? 
            WHERE bed_id = ? AND organization_id = ? AND status = 'active'
        ");
        $stmtSched->execute([$scheduledDate, $notes, $bedId, $orgId]);

        AuditService::log('schedule_checkout', 'bed', $bedId, null, ['scheduled_checkout_date' => $scheduledDate, 'notes' => $notes], $orgId, (int)$bed['building_id']);

        sendJSON(['success' => true, 'message' => "Checkout scheduled for Bed {$bed['bed_number']} on {$scheduledDate}."]);
    }
}
