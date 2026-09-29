<?php
/**
 * Admin Portal - Gate Pass & Movement Management Engine
 */

$pageTitle = "Gate Pass & Movement Management";
require_once __DIR__ . '/header.php';

$db = getDB();
$orgId = TenantContext::getOrgId();
require_once __DIR__ . '/../services/EntitlementService.php';
require_once __DIR__ . '/../services/GatePassService.php';
EntitlementService::requireAnyFeature($orgId, ['gate_pass', 'qr_gate_pass', 'visitor_management', 'check_in_out']);

$sqlPasses = "
    SELECT gp.*, u.name as tenant_name, u.phone as tenant_phone,
           u.parent_name, u.parent_phone, r.room_number, b.bed_number
    FROM gatepasses gp
    JOIN users u ON gp.tenant_id = u.id
    LEFT JOIN tenant_bookings tb ON tb.tenant_id = u.id AND tb.status = 'active'
    LEFT JOIN rooms r ON tb.room_id = r.id
    LEFT JOIN beds b ON tb.bed_id = b.id
    WHERE gp.organization_id = ?
";
$paramsPass = [$orgId];
if ($activeBldId) {
    $sqlPasses .= " AND (gp.building_id = ? OR gp.building_id IS NULL)";
    $paramsPass[] = $activeBldId;
}
$sqlPasses .= " ORDER BY gp.created_at DESC";
$stmt = $db->prepare($sqlPasses);
$stmt->execute($paramsPass);
$passes = $stmt->fetchAll();

$todayStats = GatePassService::getTodayStats($orgId, $activeBldId);
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Gate Pass &amp; Movement Management</h4>
        <p class="text-muted small mb-0">Review student/resident leave requests, track check-outs, verify return due times, and broadcast parent alerts</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="../staff/gatepass-scanner.php" target="_blank" class="btn btn-primary rounded-3 fw-bold shadow-xs">
            <i class="bi bi-qr-code-scan me-1"></i> Open Gate Pass Scanner
        </a>
        <button class="btn btn-outline-secondary rounded-3" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>
</div>

<!-- Quick Movement Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-0"><?= $todayStats['currently_out'] ?></h4>
                    <small class="text-muted">Currently Out</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning rounded-3">
                    <i class="bi bi-alarm-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-warning mb-0"><?= $todayStats['due_soon'] ?></h4>
                    <small class="text-muted">Due Soon (&lt;30m)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 border-0 shadow-sm rounded-4 bg-white <?= ($todayStats['overdue'] > 0) ? 'border border-danger' : '' ?>">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger-subtle text-danger rounded-3">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-danger mb-0"><?= $todayStats['overdue'] ?></h4>
                    <small class="text-muted">Return Overdue</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success rounded-3">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-success mb-0"><?= $todayStats['returned_today'] ?></h4>
                    <small class="text-muted">Returned Today</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gate Passes Table Card -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
            <thead class="table-light">
                <tr>
                    <th>Pass Code</th>
                    <th>Resident</th>
                    <th>Type</th>
                    <th>Out Time</th>
                    <th>Due Time</th>
                    <th>Destination &amp; Reason</th>
                    <th>Parent Alert</th>
                    <th>Movement Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passes as $p): 
                    $dueStatus = GatePassService::calculateDueStatus(
                        $p['expected_in_datetime'],
                        $p['status'],
                        $p['actual_return_datetime'] ?? $p['actual_in_datetime']
                    );
                    $outDisplay = !empty($p['actual_out_datetime']) ? $p['actual_out_datetime'] : $p['out_datetime'];
                ?>
                <tr>
                    <td class="fw-bold text-dark font-monospace"><?= htmlspecialchars($p['pass_code']) ?></td>
                    <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($p['tenant_name']) ?></div>
                        <small class="text-muted">Room <?= htmlspecialchars($p['room_number'] ?? 'N/A') ?> (<?= htmlspecialchars($p['bed_number'] ?? '') ?>)</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border text-uppercase">
                            <?= str_replace('_', ' ', $p['pass_type']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark"><?= date('d M Y', strtotime($outDisplay)) ?></div>
                        <small class="text-muted"><?= date('h:i A', strtotime($outDisplay)) ?></small>
                    </td>
                    <td>
                        <div class="fw-semibold <?= $dueStatus['is_overdue'] ? 'text-danger' : 'text-dark' ?>"><?= date('d M Y', strtotime($p['expected_in_datetime'])) ?></div>
                        <small class="<?= $dueStatus['is_overdue'] ? 'text-danger fw-bold' : 'text-muted' ?>"><?= date('h:i A', strtotime($p['expected_in_datetime'])) ?></small>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="<?= htmlspecialchars($p['destination']) ?>">
                            <?= htmlspecialchars($p['destination']) ?>
                        </div>
                        <small class="text-muted text-truncate d-block" style="max-width: 170px;" title="<?= htmlspecialchars($p['reason']) ?>">
                            <?= htmlspecialchars($p['reason']) ?>
                        </small>
                    </td>
                    <td>
                        <?php if ($p['parent_alert_sent']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-whatsapp me-1"></i> Alert Sent
                            </span>
                        <?php else: ?>
                            <button class="btn btn-xs btn-outline-success py-0 px-2 rounded-2" onclick="triggerParentAlert(<?= $p['id'] ?>)" style="font-size: 11px;">
                                <i class="bi bi-send me-1"></i> Send Alert
                            </button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge rounded-pill <?= $dueStatus['badge_class'] ?>">
                            <?= $dueStatus['label'] ?>
                        </span>
                        <?php if (!empty($dueStatus['duration_text'])): ?>
                            <small class="d-block text-muted" style="font-size: 10.5px;"><?= $dueStatus['duration_text'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?php if ($p['status'] === 'pending'): ?>
                            <button class="btn btn-sm btn-success rounded-3 me-1" onclick="updatePassStatus(<?= $p['id'] ?>, 'approved')">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger rounded-3" onclick="updatePassStatus(<?= $p['id'] ?>, 'rejected')">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        <?php elseif ($p['status'] === 'approved'): ?>
                            <button class="btn btn-sm btn-primary rounded-3" onclick="markPassOut(<?= $p['id'] ?>)">
                                <i class="bi bi-box-arrow-right me-1"></i> Mark Out
                            </button>
                        <?php elseif ($p['status'] === 'out'): ?>
                            <button class="btn btn-sm <?= $dueStatus['is_overdue'] ? 'btn-danger' : 'btn-secondary' ?> rounded-3" onclick="markPassReturned(<?= $p['id'] ?>)">
                                <i class="bi bi-box-arrow-in-left me-1"></i> Mark Returned
                            </button>
                        <?php else: ?>
                            <span class="text-muted small fw-semibold"><i class="bi bi-check2 text-success"></i> Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($passes)): ?>
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">No gate pass requests found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADMIN WHATSAPP PARENT ALERT & LOGS -->
<!-- ========================================== -->
<div class="modal fade" id="adminWhatsappModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom pb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-whatsapp fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Parent WhatsApp Alert</h5>
                        <small class="text-muted" id="adminModalSubTitle">Gate Pass #GP-XXXX</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Resident & Parent Info Header -->
                <div class="bg-light p-3 rounded-3 border mb-3 small">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Resident:</span>
                            <strong class="text-dark" id="admResidentName">Resident Name</strong>
                            <div class="text-muted" id="admResidentRoom">Room 101</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Parent / Guardian:</span>
                            <strong class="text-dark" id="admParentName">Parent Name</strong>
                            <div class="font-monospace text-success fw-semibold" id="admParentPhone">+91 XXXXX XXXXX</div>
                        </div>
                    </div>
                </div>

                <!-- Missing Contact Alert -->
                <div id="admMissingContactAlert" class="alert alert-warning p-3 rounded-3 small border-0 mb-3" style="display: none;">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                    <strong>Parent Contact Missing:</strong> No parent/guardian phone number is registered for this resident. Please update the resident profile in <a href="tenants.php" class="alert-link">Tenants Management</a>.
                </div>

                <!-- Tabs -->
                <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-3 small" id="whatsappTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-3 fw-semibold py-1.5" id="tab-template-btn" data-bs-toggle="pill" data-bs-target="#tab-template" type="button">
                            <i class="bi bi-card-checklist me-1"></i> Template Message
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-3 fw-semibold py-1.5" id="tab-custom-btn" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button">
                            <i class="bi bi-pencil-square me-1"></i> Custom Message
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-3 fw-semibold py-1.5" id="tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history" type="button" onclick="loadDeliveryHistory()">
                            <i class="bi bi-clock-history me-1"></i> Delivery History
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="whatsappTabContent">
                    <!-- Tab 1: Template Message -->
                    <div class="tab-pane fade show active" id="tab-template" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Select Message Event:</label>
                            <select class="form-select rounded-3 small" id="admTemplateEventSelect" onchange="changeAdminTemplatePreview()">
                                <option value="OUT">Departure Alert (Resident Marked Out)</option>
                                <option value="RETURNED">Return Confirmation (Resident Returned)</option>
                                <option value="OVERDUE">Overdue Notice (Resident Not Returned on Time)</option>
                                <option value="APPROVED">Pass Approval Notification</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Message Preview:</label>
                            <div class="p-3 bg-light border rounded-3 font-monospace small text-break" id="admTemplatePreviewBox" style="white-space: pre-wrap; max-height: 180px; overflow-y: auto;">
                                Loading preview...
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border flex-fill rounded-3 fw-semibold" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success flex-fill rounded-3 fw-bold" id="btnAdminSendTemplate" onclick="dispatchAdminTemplate()">
                                <i class="bi bi-send-fill me-1"></i> Send Template WhatsApp
                            </button>
                            <a href="#" target="_blank" class="btn btn-outline-success flex-fill rounded-3 fw-bold" id="btnAdminOpenWaMe" style="display: none;" onclick="logAdminManualWaOpen()">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Open WhatsApp Web/App
                            </a>
                        </div>
                    </div>

                    <!-- Tab 2: Custom Message -->
                    <div class="tab-pane fade" id="tab-custom" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Compose Custom Message:</label>
                            <textarea class="form-control rounded-3 small" id="admCustomMsgText" rows="4" maxlength="1000" placeholder="Type personal message for parent..." oninput="updateAdminCharCount()"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted" style="font-size: 11px;">Max 1000 characters</small>
                                <small class="text-muted font-monospace" style="font-size: 11px;" id="admCharCountDisplay">0 / 1000</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border flex-fill rounded-3 fw-semibold" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success flex-fill rounded-3 fw-bold" id="btnAdminSendCustom" onclick="dispatchAdminCustomMsg()">
                                <i class="bi bi-send-fill me-1"></i> Send Personal Message
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Delivery History -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel">
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-bordered align-middle small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date &amp; Time</th>
                                        <th>Type</th>
                                        <th>Recipient Phone</th>
                                        <th>Status</th>
                                        <th>Sent By</th>
                                    </tr>
                                </thead>
                                <tbody id="admHistoryTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Loading logs...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
let currentAdminPassId = 0;
let currentAdminPreviewData = null;

async function updatePassStatus(passId, status) {
    Swal.fire({
        title: `Mark as ${status.toUpperCase()}?`,
        text: (status === 'approved') ? 'This will approve the pass so staff can scan and verify departure.' : 'Are you sure?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Yes, Confirm'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch('../api/gatepass/approve.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ gatepass_id: passId, status: status })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire('Updated!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Connection error.', 'error');
            }
        }
    });
}

async function markPassOut(passId) {
    Swal.fire({
        title: 'Confirm Departure?',
        text: 'Record physical check-out for this resident?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Confirm & Mark Out'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch('../api/gatepass/mark_out.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ gatepass_id: passId })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire('Departure Logged!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Connection error.', 'error');
            }
        }
    });
}

async function markPassReturned(passId) {
    Swal.fire({
        title: 'Confirm Return?',
        text: 'Confirm resident has returned to property?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Confirm Return'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch('../api/gatepass/mark_returned.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ gatepass_id: passId })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire('Return Logged!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Connection error.', 'error');
            }
        }
    });
}

async function triggerParentAlert(passId) {
    currentAdminPassId = passId;
    Swal.fire({ title: 'Loading...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const res = await fetch(`../api/gatepass/parent_alert.php?action=preview&gatepass_id=${passId}&event_type=OUT`);
        const data = await res.json();
        Swal.close();

        if (!data.success && data.code !== 'PARENT_CONTACT_MISSING') {
            Swal.fire('Error', data.message || 'Could not load details.', 'error');
            return;
        }

        currentAdminPreviewData = data;
        const ctx = data.context || {};

        document.getElementById('adminModalSubTitle').innerText = `Gate Pass #${ctx.pass_code || 'N/A'}`;
        document.getElementById('admResidentName').innerText = ctx.resident_name || 'Resident';
        document.getElementById('admResidentRoom').innerText = `Room ${ctx.room_number || 'N/A'} (Bed ${ctx.bed_number || 'N/A'})`;
        document.getElementById('admParentName').innerText = ctx.parent_name || 'Parent / Guardian';

        const phoneDisplay = document.getElementById('admParentPhone');
        const missingNotice = document.getElementById('admMissingContactAlert');
        const btnSendTpl = document.getElementById('btnAdminSendTemplate');
        const btnSendCust = document.getElementById('btnAdminSendCustom');

        if (data.has_parent_phone) {
            phoneDisplay.innerText = data.formatted_phone || data.parent_phone;
            phoneDisplay.className = 'font-monospace text-success fw-semibold';
            missingNotice.style.display = 'none';
            btnSendTpl.disabled = false;
            btnSendCust.disabled = false;
        } else {
            phoneDisplay.innerText = 'Not Available';
            phoneDisplay.className = 'font-monospace text-danger fw-semibold';
            missingNotice.style.display = 'block';
            btnSendTpl.disabled = true;
            btnSendCust.disabled = true;
        }

        // Set default template preview
        document.getElementById('admTemplateEventSelect').value = 'OUT';
        document.getElementById('admTemplatePreviewBox').innerText = data.preview_message || '';
        
        const btnWaMe = document.getElementById('btnAdminOpenWaMe');
        if (data.is_api_configured) {
            btnSendTpl.style.display = 'block';
            btnWaMe.style.display = 'none';
        } else {
            btnSendTpl.style.display = 'none';
            btnWaMe.style.display = 'block';
            btnWaMe.href = data.wa_url || '#';
        }

        // Reset custom msg
        document.getElementById('admCustomMsgText').value = '';
        updateAdminCharCount();

        // Switch to first tab
        const tabTrigger = new bootstrap.Tab(document.getElementById('tab-template-btn'));
        tabTrigger.show();

        const modal = new bootstrap.Modal(document.getElementById('adminWhatsappModal'));
        modal.show();

    } catch (e) {
        Swal.fire('Error', 'Connection error.', 'error');
    }
}

async function changeAdminTemplatePreview() {
    if (!currentAdminPassId) return;
    const eventType = document.getElementById('admTemplateEventSelect').value;

    try {
        const res = await fetch(`../api/gatepass/parent_alert.php?action=preview&gatepass_id=${currentAdminPassId}&event_type=${eventType}`);
        const data = await res.json();
        if (data.success) {
            document.getElementById('admTemplatePreviewBox').innerText = data.preview_message;
            currentAdminPreviewData = data;
            const btnWaMe = document.getElementById('btnAdminOpenWaMe');
            btnWaMe.href = data.wa_url || '#';
        }
    } catch (e) {
        console.error("Failed to load template preview:", e);
    }
}

async function dispatchAdminTemplate() {
    if (!currentAdminPassId) return;
    const eventType = document.getElementById('admTemplateEventSelect').value;

    const modalEl = document.getElementById('adminWhatsappModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    Swal.fire({ title: 'Sending WhatsApp Alert...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const res = await fetch('../api/gatepass/parent_alert.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'send_template',
                gatepass_id: currentAdminPassId,
                event_type: eventType
            })
        });
        const data = await res.json();
        Swal.close();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: data.already_sent ? 'Already Sent' : 'WhatsApp Alert Sent',
                text: data.message
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', data.message || 'Failed to send WhatsApp alert.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Connection error.', 'error');
    }
}

async function logAdminManualWaOpen() {
    if (!currentAdminPassId || !currentAdminPreviewData) return;
    try {
        await fetch('../api/gatepass/parent_alert.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'log_manual',
                gatepass_id: currentAdminPassId,
                message: currentAdminPreviewData.preview_message
            })
        });
    } catch (e) {}
}

function updateAdminCharCount() {
    const txt = document.getElementById('admCustomMsgText').value;
    const count = txt.length;
    const el = document.getElementById('admCharCountDisplay');
    el.innerText = `${count} / 1000`;
    el.className = count > 1000 ? 'text-danger fw-bold font-monospace' : 'text-muted font-monospace';
}

async function dispatchAdminCustomMsg() {
    if (!currentAdminPassId) return;
    const text = document.getElementById('admCustomMsgText').value.trim();

    if (!text) {
        Swal.fire({ toast: true, position: 'top', icon: 'warning', title: 'Please enter a message.', showConfirmButton: false, timer: 2000 });
        return;
    }

    if (text.length > 1000) {
        Swal.fire('Message Too Long', 'Please shorten your message to 1000 characters or fewer.', 'warning');
        return;
    }

    const modalEl = document.getElementById('adminWhatsappModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    Swal.fire({ title: 'Sending WhatsApp Message...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const res = await fetch('../api/gatepass/parent_alert.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'send_custom',
                gatepass_id: currentAdminPassId,
                message: text
            })
        });
        const data = await res.json();
        Swal.close();

        if (data.success) {
            if (data.provider === 'wa_redirect' && data.wa_url) {
                window.open(data.wa_url, '_blank');
            }
            Swal.fire('Message Processed!', data.message, 'success');
        } else {
            Swal.fire('Error', data.message || 'Failed to send message.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Connection error.', 'error');
    }
}

async function loadDeliveryHistory() {
    if (!currentAdminPassId) return;
    const tbody = document.getElementById('admHistoryTableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Loading logs...</td></tr>';

    try {
        const res = await fetch(`../api/gatepass/parent_alert.php?action=history&gatepass_id=${currentAdminPassId}`);
        const data = await res.json();

        if (!data.success || !data.logs || data.logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">No WhatsApp messages recorded for this pass yet.</td></tr>';
            return;
        }

        let html = '';
        data.logs.forEach(l => {
            let badgeClass = 'bg-secondary';
            if (l.status === 'SENT' || l.status === 'DELIVERED') badgeClass = 'bg-success';
            else if (l.status === 'OPENED_IN_WHATSAPP' || l.status === 'SIMULATED') badgeClass = 'bg-primary';
            else if (l.status === 'FAILED') badgeClass = 'bg-danger';

            const sentTime = l.created_at ? new Date(l.created_at.replace(' ', 'T')).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A';

            html += `
                <tr>
                    <td class="font-monospace text-muted">${sentTime}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(l.message_type || 'template')}</span></td>
                    <td class="font-monospace">${escapeHtml(l.phone_number || 'N/A')}</td>
                    <td><span class="badge ${badgeClass} rounded-pill">${escapeHtml(l.status)}</span></td>
                    <td class="text-truncate" style="max-width: 120px;">${escapeHtml(l.sent_by_user_name || 'Staff')}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-danger">Failed to load message logs.</td></tr>';
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>

</div><!-- Close #main-content -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

