<?php
/**
 * Admin Portal - Unified Multi-Tenant Navigation Header & Sidebar Layout
 */

declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

require_once __DIR__ . '/../config/database.php';

$admin = requireAuth();
$roleCode = $_SESSION['user_role_code'] ?? ($admin['role_code'] ?? '');

// Resident / Tenant role cannot access Admin portal
if ($roleCode === 'tenant') {
    header("Location: /resident/index.php");
    exit();
}

// Super Admin accessing /admin/ directly without impersonating should go to Super Admin dashboard
if ($roleCode === 'super_admin' && !TenantContext::isImpersonating()) {
    header("Location: /super-admin/index.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$orgId = TenantContext::getOrgId();
if (!$orgId) {
    if (TenantContext::isSuperAdmin()) {
        $db = getDB();
        $orgId = (int)$db->query("SELECT id FROM organizations WHERE subscription_status != 'archived' ORDER BY id ASC LIMIT 1")->fetchColumn() ?: 1;
        TenantContext::setOrgId($orgId);
    } else {
        header("Location: /admin/index.php");
        exit();
    }
}
$activeBldId = TenantContext::getBuildingId();
$buildings = TenantContext::getUserBuildings();
$orgInfo = TenantContext::getOrgInfo();
$bldInfo = TenantContext::getBuildingInfo();
$isImpersonating = TenantContext::isImpersonating();

// Handle building switcher in admin
if (isset($_GET['switch_bld'])) {
    $switchId = (int)$_GET['switch_bld'];
    if (TenantContext::setBuildingId($switchId)) {
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

// Load enabled feature entitlements for dynamic sidebar
require_once __DIR__ . '/../services/EntitlementService.php';
require_once __DIR__ . '/../services/NotificationService.php';
$_enabledFeatures = EntitlementService::getEnabledFeatures($orgId);
$unreadNotifCount = NotificationService::getAdminUnreadCount($orgId, $activeBldId ?: null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Console') ?> | <?= htmlspecialchars($orgInfo['company_name'] ?? APP_NAME) ?></title>
    <!-- Bootstrap 5 & Icons & JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="../assets/responsive.css">
    <!-- Chart.js & SweetAlert2 & SortableJS & Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #312e81;
            --sidebar-width: 260px;
            --bg-body: #f8fafc;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
        }
        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,0.06);
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #1e293b;
            color: #fff;
            font-weight: 800;
            font-size: 16px;
            text-decoration: none;
        }
        .sidebar-brand i {
            font-size: 22px;
            color: #818cf8;
        }
        .stayflow-brand-logo {
            width: auto;
            height: 40px;
            max-width: 180px;
            object-fit: contain;
            display: block;
            filter: brightness(1.22) drop-shadow(0 0 1px rgba(255, 255, 255, 0.8)) drop-shadow(0 1px 3px rgba(255, 255, 255, 0.25));
            transition: transform 0.2s ease;
        }
        .sidebar-menu {
            list-style: none;
            padding: 16px 12px;
            margin: 0;
        }
        .sidebar-item {
            margin-bottom: 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 12px;
            color: #94a3b8;
            font-weight: 600;
            font-size: 13.5px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sidebar-link i {
            font-size: 18px;
            width: 24px;
        }
        .sidebar-link:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
        }
        .sidebar-link.active {
            color: #fff;
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }
        /* Main Content */
        #main-content {
            margin-left: var(--sidebar-width);
            padding: 24px 32px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        /* Top Navigation */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            background: #fff;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid #edf2f7;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            min-height: 115px;
            height: 100%;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .bg-indigo-soft { background-color: #ede9fe; color: #6366f1; }
        .bg-emerald-soft { background-color: #d1fae5; color: #10b981; }
        .bg-rose-soft { background-color: #ffe4e6; color: #f43f5e; }
        .bg-amber-soft { background-color: #fef3c7; color: #f59e0b; }
        .impersonation-banner {
            background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        /* Responsive Mobile Drawer & Backdrop */
        #sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(3px);
            z-index: 1035;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 991px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
                z-index: 1050;
                transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #sidebar.show {
                margin-left: 0;
                box-shadow: 10px 0 35px rgba(0,0,0,0.5);
            }
            #sidebar-backdrop {
                display: block;
            }
            #sidebar-backdrop.show {
                opacity: 1;
                pointer-events: auto;
            }
            #main-content {
                margin-left: 0;
                padding: 16px 12px;
            }
        }
    </style>
</head>
<body>

<!-- Mobile Drawer Backdrop -->
<div id="sidebar-backdrop" onclick="toggleSidebar(false)"></div>

<!-- Sidebar Navigation -->
<nav id="sidebar">
    <!-- Mobile Drawer Header with Close Button -->
    <div class="sidebar-mobile-header">
        <div class="d-flex align-items-center gap-2">
            <a href="/" class="d-flex align-items-center logo" title="StayFlow – Smart PG Management System">
                <img src="/images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo" style="height: 34px; width: auto; max-width: 155px; object-fit: contain;">
            </a>
        </div>
        <button type="button" class="btn-close-sidebar" onclick="toggleSidebar(false)" aria-label="Close Sidebar Navigation">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <a href="/" class="sidebar-brand d-flex flex-column align-items-start gap-1 logo" title="StayFlow – Smart PG Management System">
        <img src="/images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo mb-1" style="height: 38px; width: auto; max-width: 175px; object-fit: contain;">
        <div class="text-truncate w-100">
            <div class="small fw-semibold text-white"><?= htmlspecialchars($orgInfo['company_name'] ?? APP_NAME) ?></div>
            <div class="d-flex align-items-center gap-1 mt-0">
                <small class="text-white-50 fw-normal" style="font-size: 11px;"><?= htmlspecialchars($orgInfo['plan_name'] ?? 'Starter') ?></small>
                <?php if (!empty($orgInfo['is_custom'])): ?>
                    <span class="badge bg-warning text-dark px-1 py-0" style="font-size: 9px; font-weight: 700;">CUSTOM</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="/admin/index.php" class="sidebar-link <?= in_array($currentPage, ['index', 'dashboard']) ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if (TenantContext::isSuperAdmin() || in_array('property_building_management', $_enabledFeatures)): ?>
        <li class="sidebar-item">
            <a href="/admin/buildings.php" class="sidebar-link <?= in_array($currentPage, ['buildings', 'properties']) ? 'active' : '' ?>">
                <i class="bi bi-buildings"></i>
                <span>Properties / Buildings</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || in_array('room_bed_management', $_enabledFeatures)): ?>
        <li class="sidebar-item">
            <a href="/admin/rooms.php" class="sidebar-link <?= in_array($currentPage, ['rooms', 'beds']) ? 'active' : '' ?>">
                <i class="bi bi-door-open-fill"></i>
                <span>Room &amp; Bed Grid</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['resident_management', 'resident_users', 'resident_profile', 'resident_documents', 'resident_app', 'resident_notifications', 'resident_login', 'email_otp_login'])): ?>
        <li class="sidebar-item">
            <a href="/admin/tenants.php" class="sidebar-link <?= in_array($currentPage, ['tenants', 'residents']) ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Residents Directory</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['billing_invoices', 'payment_collection', 'payment_reconciliation', 'payment_receipts', 'outstanding_dues', 'expense_management', 'financial_reports'])): ?>
        <li class="sidebar-item">
            <a href="/admin/billing/index.php" class="sidebar-link <?= (str_contains($_SERVER['PHP_SELF'] ?? '', '/billing/') || in_array($currentPage, ['payments', 'billing', 'invoices', 'expenses', 'reports'])) ? 'active' : '' ?>">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Billing &amp; Invoices</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['whatsapp_automation', 'automated_payment_reminders', 'automated_resident_notifications', 'email_notifications', 'sms_notifications', 'push_notifications'])): ?>
        <li class="sidebar-item">
            <a href="/admin/whatsapp.php" class="sidebar-link <?= ($currentPage === 'whatsapp') ? 'active' : '' ?>">
                <i class="bi bi-whatsapp text-success"></i>
                <span>WhatsApp Automation</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['maintenance_requests', 'maintenance_ticket_management', 'maintenance_kanban_board', 'maintenance_status_tracking', 'maintenance_staff_assignment', 'maintenance_reports'])): ?>
        <li class="sidebar-item">
            <a href="/admin/complaints.php" class="sidebar-link <?= ($currentPage === 'complaints') ? 'active' : '' ?>">
                <i class="bi bi-kanban-fill"></i>
                <span>Maintenance Helpdesk</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['gate_pass', 'gate_pass_management', 'gate_pass_approval_workflow', 'gate_pass_qr_verification', 'gate_pass_history'])): ?>
        <li class="sidebar-item">
            <a href="/admin/gatepass.php" class="sidebar-link <?= ($currentPage === 'gatepass') ? 'active' : '' ?>">
                <i class="bi bi-qr-code-scan"></i>
                <span>Gate Pass System</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['kitchen_management', 'meal_attendance_tracking', 'meal_menu_scheduling', 'meal_guest_count', 'meal_consumption_reports'])): ?>
        <li class="sidebar-item">
            <a href="/admin/meals.php" class="sidebar-link <?= ($currentPage === 'meals') ? 'active' : '' ?>">
                <i class="bi bi-egg-fried"></i>
                <span>Kitchen &amp; Meals</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (TenantContext::isSuperAdmin() || EntitlementService::hasAnyFeature($orgId, ['staff_management', 'staff_roles_permissions', 'staff_attendance', 'staff_shift_scheduling'])): ?>
        <li class="sidebar-item">
            <a href="/admin/staff.php" class="sidebar-link <?= ($currentPage === 'staff') ? 'active' : '' ?>">
                <i class="bi bi-person-badge"></i>
                <span>Staff &amp; Roles</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="sidebar-item">
            <a href="/admin/plans.php" class="sidebar-link <?= ($currentPage === 'plans') ? 'active' : '' ?>">
                <i class="bi bi-gem"></i>
                <span>Subscription Plan</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="/admin/referral.php" class="sidebar-link <?= ($currentPage === 'referral') ? 'active' : '' ?>">
                <i class="bi bi-gift text-warning"></i>
                <span>Refer &amp; Earn</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="/admin/feature_upgrade.php" class="sidebar-link <?= ($currentPage === 'feature_upgrade') ? 'active' : '' ?>">
                <i class="bi bi-stars text-info"></i>
                <span>Upgrade Features</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 13px;">
                    <?= strtoupper(substr($admin['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div style="font-size: 12px; line-height: 1.2;">
                    <div class="fw-semibold text-white"><?= htmlspecialchars($admin['name'] ?? 'Admin User') ?></div>
                    <div class="text-white-50"><?= htmlspecialchars($roleCode) ?></div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light border-0 rounded-circle d-flex align-items-center justify-content-center p-0 text-white-50" style="width: 32px; height: 32px;" onclick="logoutUser()" title="Sign Out" aria-label="Sign Out of Admin Portal">
                <i class="bi bi-box-arrow-right fs-6"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Main Page Wrapper -->
<div id="main-content">
    <!-- Top Bar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" onclick="toggleSidebar()" aria-label="Toggle Sidebar Navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <a href="/" class="d-lg-none d-flex align-items-center logo" title="StayFlow – Smart PG Management System">
                <img src="/images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo" style="height: 30px; width: auto; max-width: 120px; object-fit: contain;">
            </a>
            <div>
                <h1 class="h5 fw-bold mb-0 text-dark"><?= $pageTitle ?? 'Admin Dashboard' ?></h1>
                <small class="text-muted"><?= date('l, d F Y') ?> | Session Active</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Notification Bell Dropdown Component -->
            <?php require __DIR__ . '/includes/notification-bell.php'; ?>

            <!-- Property / Campus Switcher Dropdown (Supports Multi-Property & Add Property Action) -->
            <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle fw-bold text-dark rounded-3 px-3 py-2 shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-buildings text-primary me-1"></i> <?= htmlspecialchars($bldInfo['building_name'] ?? 'Main Campus') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="min-width: 220px;">
                    <li class="dropdown-header small text-uppercase text-muted fw-bold pb-1">Select Property / Campus</li>
                    <?php foreach ($buildings as $b): ?>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2 <?= ($b['id'] == $activeBldId) ? 'active fw-bold' : '' ?>" href="?switch_bld=<?= $b['id'] ?>">
                            <span>
                                <?php if ($b['id'] == $activeBldId): ?><i class="bi bi-check2 text-primary me-1"></i><?php endif; ?>
                                <?= htmlspecialchars($b['building_name']) ?>
                            </span>
                            <small class="badge bg-light text-dark font-monospace"><?= $b['building_code'] ?></small>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold text-primary" href="javascript:void(0)" onclick="openGlobalAddPropertyModal()">
                            <i class="bi bi-plus-circle-fill"></i> Add Property / Building
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 small text-muted" href="/admin/buildings.php">
                            <i class="bi bi-gear-fill"></i> Manage All Properties
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

<!-- ========================================== -->
<!-- GLOBAL MODAL: ADD NEW PROPERTY / BUILDING -->
<!-- ========================================== -->
<div class="modal fade" id="globalAddPropertyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-buildings text-primary me-2"></i> Add New Property / Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="globalAddPropertyForm" onsubmit="submitGlobalAddProperty(event)">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Property / PG Name *</label>
                            <input type="text" name="building_name" id="gPropName" class="form-control rounded-3" placeholder="e.g. Koramangala Campus / Tower B" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Property Category *</label>
                            <select name="property_type" id="gPropType" class="form-select rounded-3">
                                <option value="co_living" selected>Co-Living Space</option>
                                <option value="boys_pg">Boys PG</option>
                                <option value="girls_pg">Girls PG</option>
                                <option value="student_hostel">Student Hostel</option>
                                <option value="serviced_apartments">Serviced Apartments</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label fw-semibold small">Full Address *</label>
                            <input type="text" name="address" id="gPropAddress" class="form-control rounded-3" placeholder="Street name, landmark, locality" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">City *</label>
                            <input type="text" name="city" id="gPropCity" class="form-control rounded-3" value="Bengaluru" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">State</label>
                            <input type="text" name="state" id="gPropState" class="form-control rounded-3" value="Karnataka">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">PIN Code</label>
                            <input type="text" name="pincode" id="gPropPincode" class="form-control rounded-3" value="560001">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Total Floors</label>
                            <input type="number" name="total_floors" id="gPropFloors" class="form-control rounded-3" value="3" min="1" max="15">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Contact Number</label>
                            <input type="tel" name="contact_phone" id="gPropPhone" class="form-control rounded-3" placeholder="e.g. 9876543210">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Curfew Time</label>
                            <input type="time" name="curfew_time" id="gPropCurfew" class="form-control rounded-3" value="22:30:00">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">UPI ID (VPA) for Rent</label>
                            <input type="text" name="upi_vpa" id="gPropUpi" class="form-control rounded-3" placeholder="e.g. pg@okhdfcbank">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Property Manager Name</label>
                            <input type="text" name="manager_name" id="gPropManager" class="form-control rounded-3" placeholder="Optional Manager Name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" id="btnSaveGlobalProp">
                        <i class="bi bi-plus-lg me-1"></i> Create Property
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSidebar(forceState) {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!sidebar) return;

    const willShow = (typeof forceState === 'boolean') ? forceState : !sidebar.classList.contains('show');
    if (willShow) {
        sidebar.classList.add('show');
        if (backdrop) backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#sidebar .sidebar-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                toggleSidebar(false);
            }
        });
    });
});

function logoutUser() {
    Swal.fire({
        title: 'Sign Out?',
        text: 'Are you sure you want to exit the management console?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Yes, Sign Out'
    }).then((res) => {
        if (res.isConfirmed) {
            window.location.href = '/logout.php';
        }
    });
}

// --- PROPERTY / CAMPUS CREATION HANDLER ---
function openGlobalAddPropertyModal() {
    const modalEl = document.getElementById('globalAddPropertyModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

async function submitGlobalAddProperty(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveGlobalProp');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';

    const payload = {
        building_name: document.getElementById('gPropName').value.trim(),
        property_type: document.getElementById('gPropType').value,
        address: document.getElementById('gPropAddress').value.trim(),
        city: document.getElementById('gPropCity').value.trim(),
        state: document.getElementById('gPropState').value.trim(),
        pincode: document.getElementById('gPropPincode').value.trim(),
        total_floors: document.getElementById('gPropFloors').value,
        contact_phone: document.getElementById('gPropPhone').value.trim(),
        curfew_time: document.getElementById('gPropCurfew').value,
        upi_vpa: document.getElementById('gPropUpi').value.trim(),
        manager_name: document.getElementById('gPropManager').value.trim()
    };

    const inBilling = window.location.pathname.includes('/admin/billing/');
    const apiUrl = inBilling ? '../../api/buildings/create.php' : '../api/buildings/create.php';
    const upgradeUrl = inBilling ? '../plans.php' : 'plans.php';

    try {
        const res = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Property Added!',
                text: data.message,
                timer: 1800,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else if (data.limit_reached) {
            Swal.fire({
                icon: 'warning',
                title: 'Property Limit Reached',
                html: `<p class="mb-3">${escapeHtml(data.message)}</p><a href="${upgradeUrl}" class="btn btn-warning rounded-3 fw-bold px-4">Upgrade Plan</a>`,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Close'
            });
        } else {
            Swal.fire('Error', data.message || 'Unable to create property.', 'error');
        }
    } catch (err) {
        Swal.fire('Error', err.message || 'Connection error.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = origHtml;
    }
}
</script>
<script>
window.STAYFLOW_APP_URL = '';
</script>
<script src="/assets/js/admin-notifications.js"></script>
<script src="/assets/js/push-client.js"></script>


