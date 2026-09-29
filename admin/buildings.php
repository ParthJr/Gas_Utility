<?php
/**
 * PG-Core Engine — Multi-Building / Multi-Property Management
 */

$pageTitle = "Properties & Buildings";
require_once __DIR__ . '/header.php';

$db = getDB();
$msg = null;
$msgType = null;
$orgId = TenantContext::getOrgId();

require_once __DIR__ . '/../services/EntitlementService.php';
EntitlementService::requireFeature($orgId, 'property_building_management');

// Handle Create / Edit Building
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // 1. CREATE BUILDING
    if ($_POST['action'] === 'create_building') {
        // Enforce Plan Limit
        $limitCheck = SubscriptionService::checkLimit($orgId, 'buildings');
        if (!$limitCheck['allowed']) {
            $msg = $limitCheck['message'] . " <a href='billing.php?page=settings' class='btn btn-warning btn-sm ms-2 rounded-3 fw-bold'>Upgrade Plan</a>";
            $msgType = "danger";
        } else {
            $name = trim($_POST['building_name']);
            $type = $_POST['property_type'] ?? 'co_living';
            $address = trim($_POST['address']);
            $city = trim($_POST['city'] ?? 'Bengaluru');
            $state = trim($_POST['state'] ?? 'Karnataka');
            $pincode = trim($_POST['pincode'] ?? '560001');
            $floors = (int)($_POST['total_floors'] ?? 1);
            $curfew = $_POST['curfew_time'] ?? '22:30:00';
            $upiVpa = trim($_POST['upi_vpa'] ?? 'property@okhdfcbank');
            $upiPayee = trim($_POST['upi_payee_name'] ?? $name);

            $bldCode = 'BLD-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 4)) . '-' . random_int(10, 99);

            $stmt = $db->prepare("
                INSERT INTO buildings (organization_id, building_code, building_name, property_type, address, city, state, pincode, total_floors, curfew_time, upi_vpa, upi_payee_name, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$orgId, $bldCode, $name, $type, $address, $city, $state, $pincode, $floors, $curfew, $upiVpa, $upiPayee]);
            $newBldId = (int)$db->lastInsertId();

            // Auto-create floors
            for ($f = 1; $f <= $floors; $f++) {
                $db->prepare("INSERT INTO floors (organization_id, building_id, floor_number, floor_name) VALUES (?, ?, ?, ?)")->execute([$orgId, $newBldId, $f, "Floor {$f}"]);
            }

            AuditService::log('building_created', 'building', $newBldId, null, ['name' => $name], $orgId, $newBldId);
            $msg = "Building '{$name}' created successfully with {$floors} floors!";
            $msgType = "success";
        }
    }

    // 2. UPDATE BUILDING
    elseif ($_POST['action'] === 'update_building') {
        $bldId = (int)$_POST['building_id'];
        $name = trim($_POST['building_name']);
        $type = $_POST['property_type'];
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $curfew = $_POST['curfew_time'];
        $status = $_POST['status'];

        $stmt = $db->prepare("
            UPDATE buildings 
            SET building_name = ?, property_type = ?, address = ?, city = ?, curfew_time = ?, status = ?
            WHERE id = ? AND organization_id = ?
        ");
        $stmt->execute([$name, $type, $address, $city, $curfew, $status, $bldId, $orgId]);

        AuditService::log('building_updated', 'building', $bldId, null, ['name' => $name, 'status' => $status], $orgId, $bldId);
        $msg = "Building '{$name}' updated successfully!";
        $msgType = "success";
    }
}

// Fetch Organization Buildings with Aggregated Stats
$stmtBlds = $db->prepare("
    SELECT b.*,
           (SELECT COUNT(*) FROM rooms WHERE building_id = b.id) as room_count,
           (SELECT COUNT(*) FROM beds WHERE building_id = b.id) as bed_count,
           (SELECT COUNT(*) FROM tenant_bookings WHERE building_id = b.id AND status = 'active') as active_residents,
           (SELECT COUNT(*) FROM complaints WHERE building_id = b.id AND status != 'resolved') as open_complaints
    FROM buildings b
    WHERE b.organization_id = ? AND b.status != 'archived'
    ORDER BY b.id ASC
");
$stmtBlds->execute([$orgId]);
$buildingsList = $stmtBlds->fetchAll();
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-4" role="alert">
    <i class="bi <?= ($msgType === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php
$usageStats = SubscriptionService::getUsageStats($orgId);
$bldUsage = $usageStats['resources']['buildings'] ?? ['current' => 0, 'max' => 0];
$bldLeft = max(0, $bldUsage['max'] - $bldUsage['current']);
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Properties & Buildings Directory</h4>
        <p class="text-muted small mb-0">Manage multi-property co-living locations, floors, curfew rules, and operational health</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-3">
            <i class="bi bi-buildings-fill me-1"></i> <?= $bldUsage['current'] ?>/<?= $bldUsage['max'] ?> Properties (<?= $bldLeft ?> left)
        </span>
        <button class="btn btn-primary rounded-3 fw-bold" data-bs-toggle="modal" data-bs-target="#createBuildingModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Building
        </button>
    </div>
</div>

<!-- Buildings Cards Grid -->
<div class="row g-4">
    <?php foreach ($buildingsList as $bld): 
        $occupancyPct = ($bld['bed_count'] > 0) ? round(($bld['active_residents'] / $bld['bed_count']) * 100, 1) : 0;
    ?>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($bld['building_code']) ?></span>
                    <h5 class="fw-bold text-dark mt-1 mb-0"><?= htmlspecialchars($bld['building_name']) ?></h5>
                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($bld['address']) ?>, <?= htmlspecialchars($bld['city']) ?></small>
                </div>
                <span class="badge rounded-pill <?= ($bld['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                    <?= strtoupper($bld['status']) ?>
                </span>
            </div>

            <!-- Stats Bar -->
            <div class="row g-2 text-center my-2 p-3 bg-light rounded-3">
                <div class="col-6 col-sm-3">
                    <span class="text-muted small d-block" style="font-size: 11px;">FLOORS</span>
                    <strong class="text-dark fs-6"><?= $bld['total_floors'] ?></strong>
                </div>
                <div class="col-6 col-sm-3 border-start-sm">
                    <span class="text-muted small d-block" style="font-size: 11px;">ROOMS</span>
                    <strong class="text-dark fs-6"><?= $bld['room_count'] ?></strong>
                </div>
                <div class="col-6 col-sm-3 border-start-sm">
                    <span class="text-muted small d-block" style="font-size: 11px;">TOTAL BEDS</span>
                    <strong class="text-dark fs-6"><?= $bld['bed_count'] ?></strong>
                </div>
                <div class="col-6 col-sm-3 border-start-sm">
                    <span class="text-muted small d-block" style="font-size: 11px;">OCCUPANCY</span>
                    <strong class="text-primary fs-6"><?= $occupancyPct ?>%</strong>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center text-muted small my-2">
                <span><i class="bi bi-clock me-1"></i> Curfew: <strong><?= date('h:i A', strtotime($bld['curfew_time'])) ?></strong></span>
                <span><i class="bi bi-tools me-1 text-danger"></i> Open Tickets: <strong><?= $bld['open_complaints'] ?></strong></span>
            </div>

            <div class="d-flex gap-2 mt-3 pt-3 border-top">
                <a href="rooms.php?building_id=<?= $bld['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill rounded-3 fw-semibold">
                    <i class="bi bi-door-open me-1"></i> Manage Rooms
                </a>
                <button class="btn btn-sm btn-light rounded-3 px-3" onclick='openEditBuildingModal(<?= json_encode($bld) ?>)'>
                    <i class="bi bi-gear me-1"></i> Settings
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ========================================== -->
<!-- MODAL: CREATE BUILDING -->
<!-- ========================================== -->
<div class="modal fade" id="createBuildingModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-buildings text-primary me-2"></i> Add New Building / Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_building">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Building / Branch Name *</label>
                            <input type="text" name="building_name" class="form-control rounded-3" placeholder="e.g. Tower B / Koramangala Branch" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Property Category</label>
                            <select name="property_type" class="form-select rounded-3">
                                <option value="co_living" selected>Co-Living Space</option>
                                <option value="boys_pg">Boys PG</option>
                                <option value="girls_pg">Girls PG</option>
                                <option value="student_hostel">Student Hostel</option>
                                <option value="serviced_apartments">Serviced Apartments</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label fw-semibold small">Full Address *</label>
                            <input type="text" name="address" class="form-control rounded-3" placeholder="Street name, landmark" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">City *</label>
                            <input type="text" name="city" class="form-control rounded-3" value="Bengaluru" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">State</label>
                            <input type="text" name="state" class="form-control rounded-3" value="Karnataka">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Pincode</label>
                            <input type="text" name="pincode" class="form-control rounded-3" value="560001">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Total Floors</label>
                            <input type="number" name="total_floors" class="form-control rounded-3" value="3" min="1" max="15">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Curfew Time</label>
                            <input type="time" name="curfew_time" class="form-control rounded-3" value="22:30:00">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">UPI ID (VPA)</label>
                            <input type="text" name="upi_vpa" class="form-control rounded-3" placeholder="e.g. pg@okhdfcbank">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4">Create Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT BUILDING -->
<!-- ========================================== -->
<div class="modal fade" id="editBuildingModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Property <span id="edBldName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_building">
                <input type="hidden" name="building_id" id="edBldId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Building Name *</label>
                            <input type="text" name="building_name" id="edName" class="form-control rounded-3" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Property Category</label>
                            <select name="property_type" id="edType" class="form-select rounded-3">
                                <option value="co_living">Co-Living Space</option>
                                <option value="boys_pg">Boys PG</option>
                                <option value="girls_pg">Girls PG</option>
                                <option value="student_hostel">Student Hostel</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label fw-semibold small">Address</label>
                            <input type="text" name="address" id="edAddress" class="form-control rounded-3" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">City</label>
                            <input type="text" name="city" id="edCity" class="form-control rounded-3" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Curfew Time</label>
                            <input type="time" name="curfew_time" id="edCurfew" class="form-control rounded-3">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Status</label>
                            <select name="status" id="edStatus" class="form-select rounded-3">
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditBuildingModal(bld) {
    document.getElementById('edBldId').value = bld.id;
    document.getElementById('edBldName').innerText = bld.building_name;
    document.getElementById('edName').value = bld.building_name;
    document.getElementById('edType').value = bld.property_type;
    document.getElementById('edAddress').value = bld.address;
    document.getElementById('edCity').value = bld.city;
    document.getElementById('edCurfew').value = bld.curfew_time;
    document.getElementById('edStatus').value = bld.status;
    new bootstrap.Modal(document.getElementById('editBuildingModal')).show();
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
