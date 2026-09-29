<?php
/**
 * Admin Portal - Executive Analytics Dashboard
 */

$pageTitle = "Executive Dashboard";
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../services/SubscriptionService.php';
require_once __DIR__ . '/../services/PosterService.php';

$db = getDB();
$orgId = TenantContext::getOrgId();

// Load usage details & entitlements
$usage = SubscriptionService::getUsageStats($orgId);
$entitlementSummary = EntitlementService::getEntitlementSummary($orgId);
$groupedServices = EntitlementService::getEnabledFeaturesGrouped($orgId);

// Load promotional banners & log impressions
$activePosters = PosterService::getActivePosters($orgId, 'tenant_dashboard');
foreach ($activePosters as $p) {
    PosterService::recordImpression($p['id'], $orgId, (int)($_SESSION['user_id'] ?? null));
}

// 1. Occupancy & Beds Stats
$sqlBeds = "
    SELECT 
        COUNT(id) as total_beds,
        SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_beds,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_beds,
        SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maint_beds
    FROM beds
    WHERE organization_id = ?
";
$paramsBeds = [$orgId];
if ($activeBldId) {
    $sqlBeds .= " AND building_id = ?";
    $paramsBeds[] = $activeBldId;
}
$stmtBeds = $db->prepare($sqlBeds);
$stmtBeds->execute($paramsBeds);
$bedStats = $stmtBeds->fetch();
$totalBeds = (int)($bedStats['total_beds'] ?? 0);
$occupiedBeds = (int)($bedStats['occupied_beds'] ?? 0);
$availableBeds = (int)($bedStats['available_beds'] ?? 0);
$occupancyRate = ($totalBeds > 0) ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

// 2. Financials for current month
$currentMonth = date('Y-m');
$sqlFin = "
    SELECT 
        SUM(total_amount) as total_billed,
        SUM(paid_amount) as total_collected,
        SUM(CASE WHEN status IN ('pending', 'partial', 'overdue') THEN (total_amount - paid_amount) ELSE 0 END) as total_pending,
        COUNT(CASE WHEN status IN ('pending', 'overdue') THEN 1 END) as pending_invoice_count
    FROM invoices
    WHERE organization_id = ? AND billing_month = ?
";
$paramsFin = [$orgId, $currentMonth];
if ($activeBldId) {
    $sqlFin .= " AND building_id = ?";
    $paramsFin[] = $activeBldId;
}
$stmtFin = $db->prepare($sqlFin);
$stmtFin->execute($paramsFin);
$finStats = $stmtFin->fetch();
$totalBilled = (float)($finStats['total_billed'] ?? 0);
$totalCollected = (float)($finStats['total_collected'] ?? 0);
$totalPending = (float)($finStats['total_pending'] ?? 0);

// 3. Open Complaints
$sqlComp = "SELECT COUNT(*) FROM complaints WHERE organization_id = ? AND status IN ('open', 'in_progress')";
$paramsComp = [$orgId];
if ($activeBldId) {
    $sqlComp .= " AND building_id = ?";
    $paramsComp[] = $activeBldId;
}
$stmtComp = $db->prepare($sqlComp);
$stmtComp->execute($paramsComp);
$openComplaints = (int)$stmtComp->fetchColumn();

// 4. Today's Meal Headcount
$todayDate = date('Y-m-d');
$sqlMeal = "
    SELECT 
        SUM(CASE WHEN breakfast = 0 THEN 1 ELSE 0 END) as b_skip,
        SUM(CASE WHEN lunch = 0 THEN 1 ELSE 0 END) as l_skip,
        SUM(CASE WHEN dinner = 0 THEN 1 ELSE 0 END) as d_skip
    FROM meal_attendance WHERE organization_id = ? AND date = ?
";
$paramsMeal = [$orgId, $todayDate];
if ($activeBldId) {
    $sqlMeal .= " AND building_id = ?";
    $paramsMeal[] = $activeBldId;
}
$stmtMeal = $db->prepare($sqlMeal);
$stmtMeal->execute($paramsMeal);
$mealSkips = $stmtMeal->fetch();
$bAttending = max(0, $occupiedBeds - (int)($mealSkips['b_skip'] ?? 0));
$lAttending = max(0, $occupiedBeds - (int)($mealSkips['l_skip'] ?? 0));
$dAttending = max(0, $occupiedBeds - (int)($mealSkips['d_skip'] ?? 0));

// 5. Recent Invoices
$sqlRecentInv = "
    SELECT i.*, u.name as tenant_name, r.room_number 
    FROM invoices i 
    JOIN users u ON i.tenant_id = u.id 
    JOIN rooms r ON i.room_id = r.id 
    WHERE i.organization_id = ?
";
$paramsRecentInv = [$orgId];
if ($activeBldId) {
    $sqlRecentInv .= " AND i.building_id = ?";
    $paramsRecentInv[] = $activeBldId;
}
$sqlRecentInv .= " ORDER BY i.created_at DESC LIMIT 5";
$stmtRecentInv = $db->prepare($sqlRecentInv);
$stmtRecentInv->execute($paramsRecentInv);
$recentInvoices = $stmtRecentInv->fetchAll();

// 6. Recent Complaints
$sqlRecentComp = "
    SELECT c.*, u.name as tenant_name, r.room_number 
    FROM complaints c 
    JOIN users u ON c.tenant_id = u.id 
    LEFT JOIN rooms r ON c.room_id = r.id 
    WHERE c.organization_id = ?
";
$paramsRecentComp = [$orgId];
if ($activeBldId) {
    $sqlRecentComp .= " AND c.building_id = ?";
    $paramsRecentComp[] = $activeBldId;
}
$sqlRecentComp .= " ORDER BY c.created_at DESC LIMIT 5";
$stmtRecentComp = $db->prepare($sqlRecentComp);
$stmtRecentComp->execute($paramsRecentComp);
$recentComplaints = $stmtRecentComp->fetchAll();

// 7. Live Monthly Revenue & Collections Data for Chart (Current Year)
$currentYear = (int)date('Y');
$monthlyBilled = array_fill(1, 12, 0.0);
$monthlyCollected = array_fill(1, 12, 0.0);

// Live Billed Invoices by Month
$sqlMonthlyBilled = "
    SELECT 
        CAST(SUBSTRING(billing_month, 6, 2) AS UNSIGNED) as m_num,
        SUM(total_amount) as billed_sum
    FROM invoices
    WHERE organization_id = ? AND billing_month LIKE ?
";
$paramsMB = [$orgId, "{$currentYear}-%"];
if ($activeBldId) {
    $sqlMonthlyBilled .= " AND building_id = ?";
    $paramsMB[] = $activeBldId;
}
$sqlMonthlyBilled .= " GROUP BY m_num";
$stmtMB = $db->prepare($sqlMonthlyBilled);
$stmtMB->execute($paramsMB);
while ($row = $stmtMB->fetch()) {
    $m = (int)$row['m_num'];
    if ($m >= 1 && $m <= 12) {
        $monthlyBilled[$m] = (float)$row['billed_sum'];
    }
}

// Fallback for any invoices without explicit billing_month (use created_at)
$sqlFallbackBilled = "
    SELECT 
        MONTH(created_at) as m_num,
        SUM(total_amount) as billed_sum
    FROM invoices
    WHERE organization_id = ? AND YEAR(created_at) = ? AND (billing_month IS NULL OR billing_month = '')
";
$paramsFB = [$orgId, $currentYear];
if ($activeBldId) {
    $sqlFallbackBilled .= " AND building_id = ?";
    $paramsFB[] = $activeBldId;
}
$sqlFallbackBilled .= " GROUP BY MONTH(created_at)";
$stmtFB = $db->prepare($sqlFallbackBilled);
$stmtFB->execute($paramsFB);
while ($row = $stmtFB->fetch()) {
    $m = (int)$row['m_num'];
    if ($m >= 1 && $m <= 12) {
        $monthlyBilled[$m] += (float)$row['billed_sum'];
    }
}

// Live Collected Payments by Month
$sqlMonthlyColl = "
    SELECT 
        MONTH(payment_date) as m_num,
        SUM(amount) as collected_sum
    FROM payments
    WHERE organization_id = ? AND YEAR(payment_date) = ?
";
$paramsMC = [$orgId, $currentYear];
if ($activeBldId) {
    $sqlMonthlyColl .= " AND building_id = ?";
    $paramsMC[] = $activeBldId;
}
$sqlMonthlyColl .= " GROUP BY MONTH(payment_date)";
$stmtMC = $db->prepare($sqlMonthlyColl);
$stmtMC->execute($paramsMC);
while ($row = $stmtMC->fetch()) {
    $m = (int)$row['m_num'];
    if ($m >= 1 && $m <= 12) {
        $monthlyCollected[$m] = (float)$row['collected_sum'];
    }
}

// If payments table has 0, check invoice paid_amount by billing_month
if (array_sum($monthlyCollected) == 0) {
    $sqlInvPaid = "
        SELECT 
            CAST(SUBSTRING(billing_month, 6, 2) AS UNSIGNED) as m_num,
            SUM(paid_amount) as paid_sum
        FROM invoices
        WHERE organization_id = ? AND billing_month LIKE ?
    ";
    $paramsIP = [$orgId, "{$currentYear}-%"];
    if ($activeBldId) {
        $sqlInvPaid .= " AND building_id = ?";
        $paramsIP[] = $activeBldId;
    }
    $sqlInvPaid .= " GROUP BY m_num";
    $stmtIP = $db->prepare($sqlInvPaid);
    $stmtIP->execute($paramsIP);
    while ($row = $stmtIP->fetch()) {
        $m = (int)$row['m_num'];
        if ($m >= 1 && $m <= 12) {
            $monthlyCollected[$m] = (float)$row['paid_sum'];
        }
    }
}

$chartBilledValues = json_encode(array_values($monthlyBilled));
$chartCollectedValues = json_encode(array_values($monthlyCollected));
$annualBilledTotal = array_sum($monthlyBilled);
$annualCollectedTotal = array_sum($monthlyCollected);
?>

<!-- SaaS Usage Warnings & Alerts -->
<?php if (!empty($usage['is_over_limit'])): ?>
<div class="alert alert-danger border-2 border-danger rounded-4 p-4 shadow-sm mb-4">
    <div class="d-flex align-items-start gap-3">
        <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
        <div>
            <h6 class="fw-bold text-dark mb-1">Organization Limit Exceeded (Over-Limit Status)</h6>
            <p class="small text-muted mb-2">Your organization currently exceeds the subscription resource quotas. Existing data is fully protected and active, but you cannot add new buildings, beds, staff, or residents until you upgrade your plan.</p>
            <a href="billing.php?page=settings" class="btn btn-danger btn-sm rounded-3 fw-bold">Upgrade Plan</a>
        </div>
    </div>
</div>
<?php else:
    $warnRes = null;
    $highestPct = 0;
    $resourcesList = $usage['resources'] ?? [];
    foreach ($resourcesList as $key => $res) {
        if (!empty($res['pct']) && $res['pct'] >= 80 && $res['pct'] > $highestPct) {
            $highestPct = $res['pct'];
            $warnRes = $res;
        }
    }
    if ($warnRes):
        $alertColor = $highestPct >= 90 ? 'alert-warning border-warning text-dark' : 'alert-info border-info';
        $alertIcon = $highestPct >= 90 ? 'bi-exclamation-circle-fill text-warning' : 'bi-info-circle-fill text-info';
        $btnText = $highestPct >= 90 ? 'Upgrade Now' : 'View Plans';
?>
<div class="alert <?= $alertColor ?> rounded-4 p-3 shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi <?= $alertIcon ?> fs-4"></i>
            <div>
                <span class="fw-bold">Usage Alert:</span> You have utilized <strong><?= $warnRes['pct'] ?>%</strong> of your <strong><?= htmlspecialchars($warnRes['label']) ?></strong> limit (<?= $warnRes['current'] ?> / <?= $warnRes['max'] ?> used).
            </div>
        </div>
        <a href="billing.php?page=settings" class="btn btn-sm btn-outline-dark rounded-3 fw-bold"><?= $btnText ?></a>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- SaaS Promotional Posters Banners -->
<?php 
$pathPrefix = '../';
require_once __DIR__ . '/../includes/promo_banner_slider.php'; 
?>

<!-- Active Custom Plan Banner -->
<?php if (!empty($usage['is_custom'])): ?>
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" style="font-size: 11px;">
                    <i class="bi bi-stars me-1"></i>CUSTOM PLAN ACTIVE
                </span>
                <span class="text-white-50 small">Bespoke Subscription Package</span>
            </div>
            <h4 class="fw-bold text-white mb-1">Your organization is powered by <?= htmlspecialchars($usage['plan_name']) ?></h4>
            <p class="text-white-50 mb-0 small">
                Custom Rate: <strong class="text-white"><?= CURRENCY_SYMBOL ?><?= number_format((float)$usage['price_monthly'], 2) ?>/month</strong> • Status: <span class="badge bg-success-subtle text-success text-uppercase fw-bold"><?= htmlspecialchars($usage['subscription_status']) ?></span>
            </p>
        </div>
        <div class="text-lg-end">
            <div class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-3 fs-6 fw-bold">
                <i class="bi bi-toggle-on text-warning me-1"></i> <?= $entitlementSummary['enabled_count'] ?> / <?= $entitlementSummary['total_count'] ?> Services Enabled
            </div>
            <div class="small text-white-50 mt-1">
                <?= $usage['resources']['buildings']['max'] ?> Properties • <?= $usage['resources']['beds']['max'] ?> Beds • <?= $usage['resources']['staff']['max'] ?> Staff • <?= $usage['resources']['residents']['max'] ?> Residents
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- SaaS Account Limits & Usage Card -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 fw-bold text-dark mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i> SaaS Plan Quotas &amp; Usage</h2>
        <?php if (!empty($usage['is_custom'])): ?>
            <span class="badge text-white fw-bold px-3 py-1 rounded-pill" style="background:#4f46e5;">
                <i class="bi bi-stars me-1 text-warning"></i><?= htmlspecialchars($usage['plan_name']) ?> (Custom)
            </span>
        <?php else: ?>
            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill text-capitalize"><?= htmlspecialchars($usage['plan_name']) ?> Plan</span>
        <?php endif; ?>
    </div>
    <div class="row g-3">
        <?php foreach ($usage['resources'] as $key => $res): 
            $barColor = 'bg-primary';
            if ($res['over_limit'] || $res['pct'] >= 100) $barColor = 'bg-danger';
            elseif ($res['pct'] >= 90) $barColor = 'bg-warning text-dark';
            elseif ($res['pct'] >= 80) $barColor = 'bg-info';
        ?>
        <div class="col-sm-6 col-md-3">
            <div class="border rounded-3 p-3 bg-light bg-opacity-50 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold" style="font-size:12px;"><?= htmlspecialchars($res['label']) ?></span>
                    <span class="small fw-bold text-dark"><?= $res['current'] ?> / <?= $res['max'] ?></span>
                </div>
                <div class="progress rounded-pill mb-1" style="height: 6px;">
                    <div class="progress-bar <?= $barColor ?> rounded-pill" style="width: <?= $res['pct'] ?>%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted" style="font-size: 11px;">Usage</small>
                    <small class="fw-bold text-dark" style="font-size: 11px;"><?= $res['pct'] ?>%</small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Dynamic Your Active Services Section -->
<?php if (!empty($groupedServices)): ?>
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                <i class="bi bi-shield-check fs-5"></i>
            </div>
            <div>
                <h2 class="h6 fw-bold text-dark mb-0">Active SaaS Entitlements</h2>
                <small class="text-muted"><?= $entitlementSummary['enabled_count'] ?> active services enabled for <?= htmlspecialchars($usage['plan_name']) ?></small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                <?= $entitlementSummary['enabled_count'] ?> / <?= $entitlementSummary['total_count'] ?> Active
            </span>
            <button class="btn btn-sm btn-light border rounded-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#activeServicesCollapse" aria-expanded="false" aria-controls="activeServicesCollapse">
                <i class="bi bi-grid me-1"></i> View Modules
            </button>
        </div>
    </div>

    <div class="collapse mt-3 pt-3 border-top" id="activeServicesCollapse">
        <div class="row g-3">
            <?php foreach ($groupedServices as $catKey => $catGroup): ?>
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 bg-light bg-opacity-50 h-100">
                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                        <i class="<?= $catGroup['icon'] ?> text-primary fs-5"></i>
                        <strong class="text-dark" style="font-size: 13px;"><?= htmlspecialchars($catGroup['label']) ?></strong>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1.5" style="font-size: 12px;">
                        <?php foreach ($catGroup['features'] as $f): ?>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 12px; flex-shrink:0;"></i>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($f['feature_name']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- KPI Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Occupancy Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-semibold" style="font-size: 13px;">Live Occupancy</span>
                <div class="fs-3 fw-bold my-1 text-dark"><?= $occupancyRate ?>%</div>
                <div class="small text-muted">
                    <span class="text-success fw-bold"><?= $occupiedBeds ?></span> occupied / <span class="text-primary fw-bold"><?= $availableBeds ?></span> available
                </div>
            </div>
            <div class="stat-icon bg-indigo-soft">
                <i class="bi bi-pie-chart-fill"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Collected -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-semibold" style="font-size: 13px;">Monthly Collections</span>
                <div class="fs-3 fw-bold my-1 text-success">₹<?= number_format($totalCollected) ?></div>
                <div class="small text-muted">Billed: ₹<?= number_format($totalBilled) ?></div>
            </div>
            <div class="stat-icon bg-emerald-soft">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <!-- Outstanding Dues -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-semibold" style="font-size: 13px;">Pending Dues</span>
                <div class="fs-3 fw-bold my-1 text-danger">₹<?= number_format($totalPending) ?></div>
                <div class="small text-danger fw-semibold"><?= (int)$finStats['pending_invoice_count'] ?> unpaid invoices</div>
            </div>
            <div class="stat-icon bg-rose-soft">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
        </div>
    </div>

    <!-- Maintenance Tickets -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-semibold" style="font-size: 13px;">Active Complaints</span>
                <div class="fs-3 fw-bold my-1 text-warning"><?= $openComplaints ?></div>
                <div class="small text-muted"><a href="complaints.php" class="text-decoration-none fw-semibold">View Kanban board →</a></div>
            </div>
            <div class="stat-icon bg-amber-soft">
                <i class="bi bi-tools"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Strip -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Operations</div>
        <div class="quick-action-btn-group d-flex flex-wrap gap-2">
            <a href="rooms.php" class="btn btn-primary btn-sm rounded-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Allocate Bed
            </a>
            <a href="tenants.php" class="btn btn-light border btn-sm rounded-3 fw-semibold">
                <i class="bi bi-person-plus text-success me-1"></i> Onboard Resident
            </a>
            <a href="billing.php" class="btn btn-light border btn-sm rounded-3 fw-semibold">
                <i class="bi bi-receipt text-primary me-1"></i> Invoices
            </a>
            <a href="meals.php" class="btn btn-light border btn-sm rounded-3 fw-semibold">
                <i class="bi bi-egg-fried text-warning me-1"></i> Kitchen
            </a>
        </div>
    </div>
</div>

<!-- Charts & Today's Meals Row -->
<div class="row g-4 mb-4">
    <!-- Financial Overview Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Revenue &amp; Collections (<?= date('Y') ?>)</h2>
                    <small class="text-muted">Year Total: Billed <strong class="text-primary">₹<?= number_format($annualBilledTotal, 0) ?></strong> | Collected <strong class="text-success">₹<?= number_format($annualCollectedTotal, 0) ?></strong></small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i> Live Data
                </span>
            </div>
            <div style="height: 280px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Live Occupancy Donut & Food Attendance -->
    <div class="col-lg-4 d-flex flex-column gap-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white flex-fill">
            <h2 class="h6 fw-bold text-dark mb-2">Occupancy Breakdown</h2>
            <div style="height: 130px; position: relative;">
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>

        <!-- Today's Kitchen Live Summary -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white flex-fill">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 fw-bold text-dark mb-0"><i class="bi bi-cup-hot text-danger me-1"></i> Today's Kitchen Headcount</h2>
                <a href="meals.php" class="small text-decoration-none fw-semibold">Manage →</a>
            </div>
            <div class="row text-center g-2 pt-1">
                <div class="col-4">
                    <div class="p-2 rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 11px;">Breakfast</small>
                        <strong class="fs-6 text-primary"><?= $bAttending ?></strong><small class="text-muted">/<?= $occupiedBeds ?></small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 11px;">Lunch</small>
                        <strong class="fs-6 text-success"><?= $lAttending ?></strong><small class="text-muted">/<?= $occupiedBeds ?></small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 11px;">Dinner</small>
                        <strong class="fs-6 text-indigo"><?= $dAttending ?></strong><small class="text-muted">/<?= $occupiedBeds ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Invoices & Complaints Tables -->
<div class="row g-4">
    <!-- Recent Invoices -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold text-dark mb-0">Recent Invoices</h2>
                <a href="billing.php" class="small text-decoration-none fw-semibold">View All →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentInvoices as $inv): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($inv['invoice_no']) ?></td>
                            <td>
                                <div><?= htmlspecialchars($inv['tenant_name']) ?></div>
                                <small class="text-muted">Room <?= htmlspecialchars($inv['room_number']) ?></small>
                            </td>
                            <td class="fw-semibold"><?= CURRENCY_SYMBOL ?><?= number_format($inv['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= ($inv['status'] === 'paid') ? 'bg-success' : (($inv['status'] === 'partial') ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                    <?= strtoupper($inv['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentInvoices)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No invoices generated yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Maintenance Tickets -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold text-dark mb-0">Recent Maintenance Requests</h2>
                <a href="complaints.php" class="small text-decoration-none fw-semibold">Open Kanban →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead class="table-light">
                        <tr>
                            <th>Issue</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentComplaints as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($c['title']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($c['tenant_name']) ?> (Rm <?= htmlspecialchars($c['room_number']) ?>)</small>
                            </td>
                            <td><span class="badge bg-light text-dark border text-uppercase"><?= $c['category'] ?></span></td>
                            <td>
                                <span class="badge <?= ($c['priority'] === 'urgent') ? 'bg-danger' : (($c['priority'] === 'high') ? 'bg-warning text-dark' : 'bg-info') ?>">
                                    <?= strtoupper($c['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= ($c['status'] === 'resolved') ? 'bg-success' : (($c['status'] === 'in_progress') ? 'bg-primary' : 'bg-secondary') ?>">
                                    <?= strtoupper(str_replace('_', ' ', $c['status'])) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentComplaints)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No active complaints. Great job!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div><!-- Close #main-content -->

<!-- Dashboard Chart Scripts -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Occupancy Donut Chart
    const ctxOcc = document.getElementById('occupancyChart').getContext('2d');
    new Chart(ctxOcc, {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Available', 'Maintenance'],
            datasets: [{
                data: [<?= $occupiedBeds ?>, <?= $availableBeds ?>, <?= (int)($bedStats['maint_beds'] ?? 0) ?>],
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', size: 12 } } }
            },
            cutout: '70%'
        }
    });

    // 2. Monthly Live Collection Bar Chart
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Billed Amount',
                    data: <?= $chartBilledValues ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.25)',
                    borderColor: '#4f46e5',
                    borderWidth: 1.5,
                    borderRadius: 6
                },
                {
                    label: 'Collected Amount',
                    data: <?= $chartCollectedValues ?>,
                    backgroundColor: '#10b981',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' } } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': ' + '<?= CURRENCY_SYMBOL ?>' + Number(context.parsed.y).toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return '<?= CURRENCY_SYMBOL ?>' + Number(value).toLocaleString('en-IN');
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
});

async function resetAllTestData() {
    const confirm = await Swal.fire({
        title: 'Wipe All Fake & Test Data?',
        text: 'This will remove all demo invoices, test payments, test beds, and demo organizations from MySQL. Super Admin login and SaaS plans will be preserved.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, Wipe Everything Clean',
        cancelButtonText: 'Cancel'
    });

    if (!confirm.isConfirmed) return;

    try {
        Swal.fire({
            title: 'Cleaning Database...',
            text: 'Wiping test tables and resetting metrics to 0...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const formData = new FormData();
        formData.append('key', 'StayFlowCleanReset2026');

        const resp = await fetch('../api/admin/clean_data.php', {
            method: 'POST',
            body: formData
        });
        const res = await resp.json();

        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: 'Clean Reset Complete!',
                text: res.message
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Request Failed', text: e.message });
    }
}
</script>
</body>
</html>
