<?php
$subcontractors = (new \App\Models\UserModel())->select('fs_users.*')
    ->join('user_roles', 'user_roles.user_id = fs_users.id')
    ->join('roles', 'roles.id = user_roles.role_id')
    ->where('roles.slug', 'subcontractor_vendor')
    ->findAll(); 
?>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card border-0 shadow-sm border-start border-4 border-primary">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fa-solid fa-truck-fast me-2 text-primary"></i>Long-Lead Material Tracking</h5>
                        <p class="text-muted small mb-0">Link materials to the schedule to automatically drive site installation activities.</p>
                    </div>
                    <button class="btn btn-primary d-flex align-items-center gap-2" onclick="openProcurementModal()">
                        <i class="fa-solid fa-plus"></i> Add Item
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Material Items Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0">Delivery Status & Schedule Impact</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3 small fw-bold text-uppercase">Material / Equipment</th>
                    <th class="small fw-bold text-uppercase">Linked Task</th>
                    <th class="small fw-bold text-uppercase">Lead Time</th>
                    <th class="small fw-bold text-uppercase">On-Site Target</th>
                    <th class="small fw-bold text-uppercase">Status</th>
                    <th class="pe-3 text-end small fw-bold text-uppercase">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($procurement_items)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No material items tracked for this project.</td></tr>
                <?php else: ?>
                    <?php foreach ($procurement_items as $item): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-primary"><?= esc($item['item_name']) ?></td>
                            <td>
                                <?php 
                                    $linkedTask = array_filter($tasks, fn($t) => $t['id'] == $item['task_id']);
                                    $task = reset($linkedTask);
                                    echo $task ? '<span class="badge bg-light text-dark border">'.$task['activity_id'].': '.$task['name'].'</span>' : '<span class="text-muted small">Not Linked</span>';
                                ?>
                            </td>
                            <td><?= $item['lead_time_days'] ?> Days</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-calendar-day text-muted"></i>
                                    <span class="<?= $item['status'] == 'delayed' ? 'text-danger fw-bold' : '' ?>">
                                        <?= date('M d, Y', strtotime($item['expected_on_site'])) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $item['status'] == 'delayed' ? 'danger' : 
                                    ($item['status'] == 'delivered' ? 'success' : 'info') 
                                ?>-subtle text-<?= 
                                    $item['status'] == 'delayed' ? 'danger' : 
                                    ($item['status'] == 'delivered' ? 'success' : 'info') 
                                ?>">
                                    <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <button class="btn btn-sm btn-icon btn-light" onclick='editProcurementItem(<?= json_encode($item) ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bid Leveling Section -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Subcontractor Bid Leveling Engine</h6>
                <a href="<?= site_url("projects/{$project['id']}/bid-leveling") ?>" class="btn btn-outline-primary btn-sm">Full Analysis <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="alert alert-info border-0 rounded-0 mb-0 small py-2 px-3">
                    <i class="fa-solid fa-info-circle me-1"></i> 
                    Market-surpassing feature: Automatically spot scope gaps by comparing sub-bids against the project BOQ.
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">BOQ Item</th>
                                <th>Bidder A</th>
                                <th>Bidder B</th>
                                <th>Bidder C</th>
                                <th class="pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Import bids to start automated leveling.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Procurement Modal -->
<div class="modal fade" id="procurementModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="procurementForm" class="modal-content border-0 shadow">
            <input type="hidden" name="id" id="procId">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Material Tracking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Material Name</label>
                    <input type="text" name="item_name" id="procName" class="form-control" placeholder="e.g. Switchgear A" required>
                </div>
                <div class="row g-2">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Target On-Site Date</label>
                        <input type="date" name="expected_on_site" id="procDate" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Lead Time (Days)</label>
                        <input type="number" name="lead_time_days" id="procLead" class="form-control" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Linked Activity (Schedule)</label>
                    <select name="task_id" id="procTask" class="form-select">
                        <option value="">-- No Link --</option>
                        <?php foreach($tasks as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['activity_id'] ?>: <?= esc($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text small">Delayed materials will automatically push successor activities.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" id="procStatus" class="form-select">
                        <option value="not_ordered">Not Ordered</option>
                        <option value="ordered">Ordered</option>
                        <option value="in_transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                        <option value="delayed text-danger">Delayed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm fw-bold">Save & Sync Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
    const procModal = new bootstrap.Modal(document.getElementById('procurementModal'));

    function openProcurementModal() {
        document.getElementById('procurementForm').reset();
        document.getElementById('procId').value = '';
        procModal.show();
    }

    function editProcurementItem(item) {
        document.getElementById('procId').value = item.id;
        document.getElementById('procName').value = item.item_name;
        document.getElementById('procTask').value = item.task_id;
        document.getElementById('procLead').value = item.lead_time_days;
        document.getElementById('procDate').value = item.expected_on_site;
        document.getElementById('procStatus').value = item.status;
        procModal.show();
    }

    document.getElementById('procurementForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append(CSRF_NAME, CSRF_TOKEN);
        fetch(`<?= site_url("projects/{$project['id']}/procurement/save") ?>`, {
            method: 'POST',
            body: fd
        }).then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
        });
    });
</script>
