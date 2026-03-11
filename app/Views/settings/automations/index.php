<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Automations (Logic Hub)</h4>
            <p class="text-muted small">Salesforce-style workflow rules for your business logic.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
            <i class="fa-solid fa-bolt me-2"></i>Create New Rule
        </button>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">Active Rules</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Rule Name</th>
                                    <th>Trigger</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rules as $rule): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?= esc($rule['name']) ?></td>
                                    <td>
                                        <small class="badge bg-info-subtle text-info border-info">
                                            <?= esc($rule['trigger_object']) ?>: <?= esc($rule['trigger_type']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($rule['action_type']) ?></small>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('settings/automations/toggle/'.$rule['id']) ?>" class="badge <?= $rule['is_active'] ? 'bg-success' : 'bg-secondary' ?> text-decoration-none">
                                            <?= $rule['is_active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-light"><i class="fa-solid fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; if (empty($rules)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">No automation rules defined.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">Execution History</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        <?php foreach ($logs as $log): ?>
                        <li class="list-group-item border-0 px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold <?= $log['status'] === 'success' ? 'text-success' : 'text-danger' ?>">
                                    <?= strtoupper($log['status']) ?>
                                </span>
                                <span class="text-muted smallest"><?= date('H:i', strtotime($log['executed_at'])) ?></span>
                            </div>
                            <div class="text-muted"><?= esc($log['message']) ?></div>
                        </li>
                        <?php endforeach; if (empty($logs)): ?>
                        <li class="list-group-item border-0 text-center py-5 text-muted">No execution logs.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Rule Modal -->
<div class="modal fade" id="addRuleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New Automation Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('settings/automations/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Rule Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Auto-Task on Won Change Order">
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Trigger Object</label>
                            <select name="trigger_object" class="form-select" required>
                                <option value="projects">Projects</option>
                                <option value="change_orders">Change Orders</option>
                                <option value="leads">Leads</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Trigger Event</label>
                            <select name="trigger_type" class="form-select" required>
                                <option value="create">On Create</option>
                                <option value="update">On Update (Status Change)</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded mb-3">
                        <div class="fw-bold small mb-2">Conditions (Optional)</div>
                        <div class="row g-2 text-muted small">
                            <div class="col-md-4">Field Name: <input type="text" name="cond_field" class="form-control form-control-sm" placeholder="status"></div>
                            <div class="col-md-3">Operator: <select name="cond_op" class="form-select form-select-sm"><option value="==">Equals</option><option value="!=">Not Equals</option></select></div>
                            <div class="col-md-5">Value: <input type="text" name="cond_val" class="form-control form-control-sm" placeholder="approved"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Action Type</label>
                        <select name="action_type" class="form-select" id="actionType" required>
                            <option value="create_task">Create Automated Task</option>
                            <option value="update_field">Update Field Value</option>
                        </select>
                    </div>

                    <div id="createTaskFields">
                        <div class="mb-3">
                            <input type="text" name="task_title" class="form-control mb-2" placeholder="Task Title">
                            <textarea name="task_desc" class="form-control" rows="2" placeholder="Task Description"></textarea>
                        </div>
                    </div>

                    <div id="updateFieldFields" style="display:none;">
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" name="action_field" class="form-control" placeholder="Field to Update"></div>
                            <div class="col-md-6"><input type="text" name="action_val" class="form-control" placeholder="New Value"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('actionType').addEventListener('change', function() {
    if (this.value === 'create_task') {
        document.getElementById('createTaskFields').style.display = 'block';
        document.getElementById('updateFieldFields').style.display = 'none';
    } else {
        document.getElementById('createTaskFields').style.display = 'none';
        document.getElementById('updateFieldFields').style.display = 'block';
    }
});
</script>

<?= $this->endSection() ?>
