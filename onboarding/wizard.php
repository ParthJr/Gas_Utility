<?php
/**
 * PG-Core Engine — 11-Step SaaS Customer Onboarding Wizard
 * Fast, self-service property setup under 10 minutes.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$db = getDB();
$msg = null;
$msgType = null;
$createdData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_onboarding') {
    try {
        $db->beginTransaction();

        $company = trim($_POST['company_name']);
        $owner = trim($_POST['owner_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $city = trim($_POST['city'] ?? 'Bengaluru');
        $bldName = trim($_POST['building_name'] ?? 'Main Tower');
        $floors = max(1, (int)($_POST['total_floors'] ?? 2));
        $roomsPerFloor = max(1, (int)($_POST['rooms_per_floor'] ?? 3));
        $bedsPerRoom = max(1, (int)($_POST['beds_per_room'] ?? 2));
        $rent = (float)($_POST['default_rent'] ?? 7000);
        $maint = (float)($_POST['maintenance_charge'] ?? 300);
        $ebRate = (float)($_POST['eb_rate'] ?? 10);
        $upiVpa = trim($_POST['upi_vpa'] ?? 'my-pg@okhdfcbank');
        $upiPayee = trim($_POST['upi_payee_name'] ?? $company);

        // 1. Create Organization
        $orgCode = 'ORG-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $company), 0, 6)) . '-' . random_int(10, 99);
        $starterPlanId = (int)$db->query("SELECT id FROM plans WHERE plan_code = 'STARTER'")->fetchColumn() ?: 1;

        $stmtOrg = $db->prepare("
            INSERT INTO organizations (organization_code, company_name, owner_name, email, phone, city, plan_id, subscription_status, trial_start, trial_end)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'trial', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))
        ");
        $stmtOrg->execute([$orgCode, $company, $owner, $email, $phone, $city, $starterPlanId]);
        $orgId = (int)$db->lastInsertId();

        // 2. Create Building
        $bldCode = 'BLD-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $bldName), 0, 4)) . '-01';
        $stmtBld = $db->prepare("
            INSERT INTO buildings (organization_id, building_code, building_name, property_type, address, city, state, pincode, total_floors, upi_vpa, upi_payee_name, maintenance_charge, electricity_unit_rate, late_fee_daily, status)
            VALUES (?, ?, ?, 'co_living', ?, ?, 'State', '560001', ?, ?, ?, ?, ?, 50.00, 'active')
        ");
        $stmtBld->execute([$orgId, $bldCode, $bldName, $city . ' Address', $city, $floors, $upiVpa, $upiPayee, $maint, $ebRate]);
        $bldId = (int)$db->lastInsertId();

        // 3. Create Floors, Rooms, and Beds
        $totalRoomsCount = 0;
        $totalBedsCount = 0;

        require_once __DIR__ . '/../services/RoomBedHelper.php';

        for ($f = 1; $f <= $floors; $f++) {
            $stmtFl = $db->prepare("INSERT INTO floors (organization_id, building_id, floor_number, floor_name) VALUES (?, ?, ?, ?)");
            $stmtFl->execute([$orgId, $bldId, $f, "Floor {$f}"]);
            $floorId = (int)$db->lastInsertId();

            for ($r = 1; $r <= $roomsPerFloor; $r++) {
                $roomNo = sprintf("%d%02d", $f, $r);
                $roomSharingType = ($bedsPerRoom === 1) ? 'single' : (($bedsPerRoom === 2) ? 'double' : (($bedsPerRoom === 3) ? 'triple' : (($bedsPerRoom === 4) ? 'four_sharing' : 'custom')));
                
                $stmtRm = $db->prepare("
                    INSERT INTO rooms (organization_id, building_id, floor_id, room_number, floor, room_type, ac_type, total_beds, base_rent)
                    VALUES (?, ?, ?, ?, ?, ?, 'AC', ?, ?)
                ");
                $stmtRm->execute([$orgId, $bldId, $floorId, $roomNo, $f, $roomSharingType, $bedsPerRoom, $rent]);
                $roomId = (int)$db->lastInsertId();
                $totalRoomsCount++;

                $bedLabels = RoomBedHelper::getBedLabels($roomNo, $roomSharingType, $bedsPerRoom);
                foreach ($bedLabels as $bedNo) {
                    $stmtBed = $db->prepare("
                        INSERT INTO beds (organization_id, building_id, room_id, bed_number, monthly_rent, status)
                        VALUES (?, ?, ?, ?, ?, 'available')
                    ");
                    $stmtBed->execute([$orgId, $bldId, $roomId, $bedNo, $rent]);
                    $totalBedsCount++;
                }
            }
        }

        // Update building totals
        $db->prepare("UPDATE buildings SET total_rooms = ?, total_beds = ? WHERE id = ?")->execute([$totalRoomsCount, $totalBedsCount, $bldId]);

        // 4. Create Owner Account
        $tempPass = AuthService::generateTemporaryPassword(10);
        $hash = password_hash($tempPass, PASSWORD_BCRYPT);
        $username = 'owner_' . strtolower(substr(preg_replace('/[^A-Za-z0-9]/', '', $company), 0, 6));

        $stmtUser = $db->prepare("
            INSERT INTO users (organization_id, building_id, role, role_id, name, username, email, phone, password, status)
            VALUES (?, ?, 'admin', 2, ?, ?, ?, ?, ?, 'active')
        ");
        $stmtUser->execute([$orgId, $bldId, $owner, $username, $email, $phone, $hash]);

        AuditService::log('onboarding_completed', 'organization', $orgId, null, [
            'company' => $company, 'rooms' => $totalRoomsCount, 'beds' => $totalBedsCount
        ]);

        $db->commit();

        $createdData = [
            'company' => $company,
            'org_code' => $orgCode,
            'username' => $username,
            'password' => $tempPass,
            'building' => $bldName,
            'rooms' => $totalRoomsCount,
            'beds' => $totalBedsCount
        ];
    } catch (Exception $e) {
        $db->rollBack();
        $msg = "Onboarding failed: " . $e->getMessage();
        $msgType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Property Setup Wizard | PG-Core Engine</title>
    <!-- Bootstrap 5, Icons, Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
        }
        .wizard-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 0;
            width: 100%;
            max-width: 800px;
        }
        .step-pill {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="wizard-card p-4 p-md-5">
    
    <?php if ($createdData): ?>
    <!-- SUCCESS COMPLETE SCREEN -->
    <div class="text-center py-4">
        <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 80px; height: 80px;">
            <i class="bi bi-check2-circle fs-1"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Your PG is Ready!</h3>
        <p class="text-muted mb-4"><strong><?= htmlspecialchars($createdData['company']) ?></strong> has been set up with <strong><?= $createdData['rooms'] ?> Rooms</strong> and <strong><?= $createdData['beds'] ?> Beds</strong>.</p>

        <div class="card bg-light border-0 rounded-4 p-4 text-start d-inline-block w-100 mb-4" style="max-width: 500px;">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Owner Login Credentials</h6>
            <div class="mb-2"><strong>Organization Code:</strong> <span class="badge bg-dark font-monospace"><?= $createdData['org_code'] ?></span></div>
            <div class="mb-2"><strong>Login Username:</strong> <code class="fs-6"><?= $createdData['username'] ?></code></div>
            <div class="mb-2"><strong>Temporary Password:</strong> <span class="text-danger fw-bold fs-6 font-monospace"><?= $createdData['password'] ?></span></div>
            <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i> Passwords are saved with bcrypt hashing. Copy this temporary password now.</div>
        </div>

        <div>
            <a href="../index.php" class="btn btn-primary btn-lg rounded-3 fw-bold px-5">
                <i class="bi bi-box-arrow-in-right me-2"></i> Log In to Property Dashboard
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- ONBOARDING FORM -->
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="rounded-3 bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
            <i class="bi bi-magic fs-4"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0 text-dark">PG-Core SaaS Onboarding Wizard</h4>
            <p class="text-muted small mb-0">Set up your entire PG/Hostel properties, rooms, beds, and billing in under 10 minutes</p>
        </div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?> rounded-4 mb-3"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="complete_onboarding">

        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-buildings me-2"></i> 1. Organization & Owner Information</h6>
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">PG / Company Name *</label>
                <input type="text" name="company_name" class="form-control rounded-3" placeholder="e.g. Royal Living Co-Living" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">Owner Full Name *</label>
                <input type="text" name="owner_name" class="form-control rounded-3" placeholder="e.g. Rajesh Kumar" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">Owner Email *</label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="rajesh@royalliving.com" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">Owner Phone Number *</label>
                <input type="tel" name="phone" class="form-control rounded-3" placeholder="7622008118" required>
            </div>
        </div>

        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-door-open me-2"></i> 2. Initial Property & Room Matrix</h6>
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">Building / Branch Name *</label>
                <input type="text" name="building_name" class="form-control rounded-3" value="Main Tower" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">City Location</label>
                <input type="text" name="city" class="form-control rounded-3" value="Bengaluru">
            </div>
            <div class="col-sm-4">
                <label class="form-label fw-semibold small">Total Floors</label>
                <input type="number" name="total_floors" class="form-control rounded-3" value="3" min="1" max="10">
            </div>
            <div class="col-sm-4">
                <label class="form-label fw-semibold small">Rooms per Floor</label>
                <input type="number" name="rooms_per_floor" class="form-control rounded-3" value="4" min="1" max="20">
            </div>
            <div class="col-sm-4">
                <label class="form-label fw-semibold small">Beds per Room</label>
                <input type="number" name="beds_per_room" class="form-control rounded-3" value="2" min="1" max="6">
            </div>
        </div>

        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-receipt me-2"></i> 3. Default Billing & Custom UPI</h6>
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <label class="form-label fw-semibold small">Default Bed Rent (₹) *</label>
                <input type="number" name="default_rent" class="form-control rounded-3" value="7500" step="100" required>
            </div>
            <div class="col-sm-4">
                <label class="form-label fw-semibold small">Monthly Maintenance (₹)</label>
                <input type="number" name="maintenance_charge" class="form-control rounded-3" value="300" step="50">
            </div>
            <div class="col-sm-4">
                <label class="form-label fw-semibold small">Electricity Unit Rate (₹)</label>
                <input type="number" name="eb_rate" class="form-control rounded-3" value="10" step="0.5">
            </div>
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">Owner UPI ID / VPA *</label>
                <input type="text" name="upi_vpa" class="form-control rounded-3" placeholder="e.g. 7622008118@paytm" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label fw-semibold small">Payee / Merchant Business Name</label>
                <input type="text" name="upi_payee_name" class="form-control rounded-3" placeholder="e.g. Royal Living PG">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-4 fw-bold py-3">
            <i class="bi bi-rocket-takeoff me-2"></i> Auto-Generate Property & Launch Dashboard
        </button>
    </form>
    <?php endif; ?>

</div>

</body>
</html>
