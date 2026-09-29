<?php
/**
 * Admin Portal - Maintenance & Complaints Kanban Board
 */

$pageTitle = "Maintenance Kanban Board";
require_once __DIR__ . '/header.php';

$db = getDB();
$orgId = TenantContext::getOrgId();
require_once __DIR__ . '/../services/EntitlementService.php';
EntitlementService::requireAnyFeature($orgId, ['maintenance_requests', 'maintenance_ticket_management', 'maintenance_kanban_board', 'maintenance_status_tracking', 'maintenance_staff_assignment']);

$sqlActiveTenants = "
    SELECT u.id, u.name, u.phone, r.room_number, b.bed_number, tb.room_id
    FROM tenant_bookings tb
    JOIN users u ON tb.tenant_id = u.id
    JOIN rooms r ON tb.room_id = r.id
    JOIN beds b ON tb.bed_id = b.id
    WHERE tb.organization_id = ? AND tb.status = 'active'
";
$paramsAT = [$orgId];
if ($activeBldId) {
    $sqlActiveTenants .= " AND tb.building_id = ?";
    $paramsAT[] = $activeBldId;
}
$sqlActiveTenants .= " ORDER BY u.name ASC";
$stmtTenants = $db->prepare($sqlActiveTenants);
$stmtTenants->execute($paramsAT);
$activeResidents = $stmtTenants->fetchAll();
?>

<!-- Header Title Strip -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Maintenance Kanban Board</h4>
        <p class="text-muted small mb-0">Drag and drop tickets between columns to update technician workflow in real-time</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#newTicketModal">
            <i class="bi bi-plus-lg me-1"></i> Lodge Maintenance Ticket
        </button>
        <button class="btn btn-outline-secondary rounded-3" onclick="loadKanban()" title="Refresh Kanban">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
    </div>
</div>

<style>
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        align-items: start;
    }
    .kanban-col {
        background: #f1f5f9;
        border-radius: 16px;
        padding: 16px;
        min-height: 550px;
        display: flex;
        flex-direction: column;
    }
    .kanban-header {
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kanban-cards-list {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-height: 100px;
    }
    .kanban-card {
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        cursor: grab;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kanban-card:active {
        cursor: grabbing;
    }
    .kanban-card.sortable-ghost {
        opacity: 0.4;
        background: #e0e7ff;
    }
</style>

<!-- Kanban Columns Grid -->
<div class="kanban-board" id="kanbanBoard">
    <!-- 1. Open Column -->
    <div class="kanban-col">
        <div class="kanban-header text-danger">
            <span><i class="bi bi-exclamation-circle-fill me-1"></i> Open Tickets</span>
            <span class="badge bg-danger rounded-pill" id="count-open">0</span>
        </div>
        <div class="kanban-cards-list" id="list-open" data-status="open"></div>
    </div>

    <!-- 2. In Progress Column -->
    <div class="kanban-col">
        <div class="kanban-header text-warning">
            <span><i class="bi bi-gear-fill me-1"></i> In Progress</span>
            <span class="badge bg-warning text-dark rounded-pill" id="count-in_progress">0</span>
        </div>
        <div class="kanban-cards-list" id="list-in_progress" data-status="in_progress"></div>
    </div>

    <!-- 3. Resolved Column -->
    <div class="kanban-col">
        <div class="kanban-header text-success">
            <span><i class="bi bi-check-circle-fill me-1"></i> Resolved</span>
            <span class="badge bg-success rounded-pill" id="count-resolved">0</span>
        </div>
        <div class="kanban-cards-list" id="list-resolved" data-status="resolved"></div>
    </div>

    <!-- 4. Closed Column -->
    <div class="kanban-col">
        <div class="kanban-header text-secondary">
            <span><i class="bi bi-archive-fill me-1"></i> Closed</span>
            <span class="badge bg-secondary rounded-pill" id="count-closed">0</span>
        </div>
        <div class="kanban-cards-list" id="list-closed" data-status="closed"></div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: LODGE NEW MAINTENANCE TICKET -->
<!-- ========================================== -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Lodge Maintenance Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newTicketForm" onsubmit="handleCreateTicket(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Select Resident *</label>
                        <select class="form-select rounded-3" id="tResidentId" required>
                            <option value="">-- Choose Resident --</option>
                            <?php foreach ($activeResidents as $ar): ?>
                            <option value="<?= $ar['id'] ?>">
                                <?= htmlspecialchars($ar['name']) ?> (Room <?= $ar['room_number'] ?> - Bed <?= $ar['bed_number'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Category *</label>
                            <select class="form-select rounded-3" id="tCategory" required>
                                <option value="electrical">⚡ Electrical</option>
                                <option value="plumbing">🚰 Plumbing</option>
                                <option value="wifi">📶 WiFi & Internet</option>
                                <option value="carpentry">🔨 Carpentry</option>
                                <option value="cleaning">🧹 Housekeeping</option>
                                <option value="appliance">❄️ AC / Appliance</option>
                                <option value="other">🔧 General / Other</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Priority Level *</label>
                            <select class="form-select rounded-3" id="tPriority" required>
                                <option value="low">🟢 Low</option>
                                <option value="medium" selected>🟡 Medium</option>
                                <option value="high">🟠 High</option>
                                <option value="urgent">🔴 Urgent / Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Issue Title *</label>
                        <input type="text" class="form-control rounded-3" id="tTitle" placeholder="e.g. Geyser heating slow, AC remote damaged" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description Details *</label>
                        <textarea class="form-control rounded-3" id="tDescription" rows="3" placeholder="Describe the problem in detail for the technician..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Assign Technician (Optional)</label>
                        <input type="text" class="form-control rounded-3" id="tAssignedTo" placeholder="e.g. Ramesh Electrician / Shyam Plumber">
                        <small class="text-muted" style="font-size: 11px;">If a technician is assigned, ticket will immediately move to <strong>In Progress</strong>.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" id="submitTicketBtn">
                        <i class="bi bi-send-fill me-1"></i> Lodge Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ASSIGN TECHNICIAN -->
<!-- ========================================== -->
<div class="modal fade" id="assignTechModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Assign Maintenance Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assignComplaintId">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Staff / Technician Name *</label>
                    <input type="text" class="form-control rounded-3" id="assignTechName" placeholder="e.g. Ramesh (Electrician) / Shyam (Plumber)" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-3 fw-bold px-4" onclick="submitAssignTech()">
                    <i class="bi bi-person-check-fill me-1"></i> Assign Staff
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let sortables = [];

async function loadKanban() {
    try {
        const res = await fetch('../api/complaints/update_status.php');
        const data = await res.json();
        if (!data.success) throw new Error('Failed to load tickets');

        const cols = ['open', 'in_progress', 'resolved', 'closed'];
        cols.forEach(status => {
            const listEl = document.getElementById(`list-${status}`);
            const countEl = document.getElementById(`count-${status}`);
            const items = data.kanban[status] || [];
            
            countEl.innerText = items.length;
            listEl.innerHTML = '';

            items.forEach(c => {
                const card = document.createElement('div');
                card.className = 'kanban-card';
                card.setAttribute('data-id', c.id);

                const priorityCls = (c.priority === 'urgent') ? 'bg-danger text-white' : ((c.priority === 'high') ? 'bg-warning text-dark' : 'bg-light text-dark border');
                
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge ${priorityCls} rounded-pill text-uppercase" style="font-size: 10px;">${c.priority}</span>
                        <span class="badge bg-light text-muted border text-uppercase" style="font-size: 10px;">${c.category}</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">${escapeHtml(c.title)}</h6>
                    <p class="text-muted small mb-2" style="font-size: 12px;">${escapeHtml(c.description)}</p>
                    
                    <div class="p-2 bg-light rounded-3 mb-2 small" style="font-size: 11.5px;">
                        <div class="fw-semibold text-dark"><i class="bi bi-person me-1"></i>${escapeHtml(c.tenant_name)}</div>
                        <div class="text-muted"><i class="bi bi-door-closed me-1"></i>Room ${c.room_number || 'N/A'}</div>
                        ${c.assigned_to ? `<div class="text-primary mt-1 fw-semibold"><i class="bi bi-wrench-adjustable me-1"></i>${escapeHtml(c.assigned_to)}</div>` : ''}
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-1 border-top" style="font-size: 11px;">
                        <span class="text-muted">${new Date(c.created_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short' })}</span>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-link btn-xs p-0 text-primary fw-semibold" onclick="openAssignModal(${c.id}, '${escapeHtml(c.assigned_to || '')}')">
                                <i class="bi bi-person-gear me-1"></i>${c.assigned_to ? 'Reassign' : 'Assign'}
                            </button>
                            <button class="btn btn-link btn-xs p-0 text-danger fw-semibold" onclick="confirmDeleteAdmin(${c.id}, '${escapeHtml(c.ticket_no || '')}', '${escapeHtml(c.title || '')}')">
                                <i class="bi bi-trash3 me-1"></i>Delete Problem
                            </button>
                        </div>
                    </div>
                `;
                listEl.appendChild(card);
            });
        });

        initSortable();
    } catch (err) {
        Swal.fire('Error', 'Could not load complaints board.', 'error');
    }
}

function initSortable() {
    sortables.forEach(s => s.destroy());
    sortables = [];

    const cols = ['open', 'in_progress', 'resolved', 'closed'];
    cols.forEach(status => {
        const el = document.getElementById(`list-${status}`);
        const sortable = new Sortable(el, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: async function (evt) {
                const itemEl = evt.item;
                const complaintId = itemEl.getAttribute('data-id');
                const targetStatus = evt.to.getAttribute('data-status');

                try {
                    const res = await fetch('../api/complaints/update_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ complaint_id: complaintId, status: targetStatus })
                    });
                    const data = await res.json();
                    if (!data.success) {
                        Swal.fire('Error', data.message, 'error');
                        loadKanban();
                    } else {
                        loadKanban();
                    }
                } catch (e) {
                    Swal.fire('Error', 'Connection error updating status.', 'error');
                    loadKanban();
                }
            }
        });
        sortables.push(sortable);
    });
}

// 1. Create New Ticket
let adminSubmissionToken = '<?= bin2hex(random_bytes(16)) ?>';

async function handleCreateTicket(e) {
    e.preventDefault();
    const btn = document.getElementById('submitTicketBtn');
    const residentId = document.getElementById('tResidentId').value;
    const category = document.getElementById('tCategory').value;
    const priority = document.getElementById('tPriority').value;
    const title = document.getElementById('tTitle').value.trim();
    const description = document.getElementById('tDescription').value.trim();
    const assignedTo = document.getElementById('tAssignedTo').value.trim();

    if (!residentId || !title || !description) {
        Swal.fire('Required Fields', 'Please select a resident and fill out title and description.', 'warning');
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Lodging...';
    }

    try {
        const res = await fetch('../api/complaints/create.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tenant_id: residentId,
                category: category,
                priority: priority,
                title: title,
                description: description,
                assigned_to: assignedTo,
                submission_token: adminSubmissionToken
            })
        });
        const data = await res.json();
        if (data.success) {
            adminSubmissionToken = Math.random().toString(36).substring(2) + Date.now().toString(36);
            bootstrap.Modal.getInstance(document.getElementById('newTicketModal')).hide();
            document.getElementById('newTicketForm').reset();
            Swal.fire({
                title: 'Ticket Lodged!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            loadKanban();
        } else {
            Swal.fire('Error', data.message || 'Failed to lodge ticket.', 'error');
        }
    } catch (err) {
        Swal.fire('Error', 'Network error creating ticket.', 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Lodge Ticket';
        }
    }
}

// 2. Assign Technician
function openAssignModal(id, currentTech) {
    document.getElementById('assignComplaintId').value = id;
    document.getElementById('assignTechName').value = currentTech;
    new bootstrap.Modal(document.getElementById('assignTechModal')).show();
}

async function submitAssignTech() {
    const id = document.getElementById('assignComplaintId').value;
    const name = document.getElementById('assignTechName').value.trim();
    if (!name) {
        Swal.fire('Error', 'Please enter a technician name.', 'error');
        return;
    }

    try {
        const res = await fetch('../api/complaints/assign.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ complaint_id: id, assigned_to: name })
        });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('assignTechModal')).hide();
            Swal.fire({
                title: 'Technician Assigned!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            loadKanban();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Connection error.', 'error');
    }
}

const adminCsrfToken = '<?= getCsrfToken() ?>';

async function confirmDeleteAdmin(id, ticketNo, title) {
    const result = await Swal.fire({
        title: 'Delete Maintenance Problem?',
        html: `<div class="text-start p-2 bg-light rounded-3 mb-2 border">
                 <div class="text-muted small">Ticket: <strong class="text-dark font-monospace">${ticketNo}</strong></div>
                 <div class="text-muted small">Problem: <strong class="text-dark">${escapeHtml(title)}</strong></div>
               </div>
               <p class="mb-0 text-muted small">Are you sure you want to permanently delete this maintenance ticket? This action cannot be undone.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash3 me-1"></i> Delete Problem',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });

    if (!result.isConfirmed) return;

    try {
        const baseUrl = window.STAYFLOW_APP_URL || '..';
        const res = await fetch(`${baseUrl}/api/complaints/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': adminCsrfToken
            },
            body: JSON.stringify({
                ticket_id: id,
                csrf_token: adminCsrfToken
            })
        });

        const data = await res.json();
        if (data.success) {
            Swal.fire({
                title: 'Deleted!',
                text: data.message || 'Maintenance problem deleted successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            loadKanban();
        } else {
            Swal.fire('Error', data.message || 'Failed to delete problem.', 'error');
            loadKanban();
        }
    } catch (err) {
        Swal.fire('Error', 'Connection error while deleting ticket.', 'error');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

document.addEventListener('DOMContentLoaded', loadKanban);
</script>

</div><!-- Close #main-content -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
