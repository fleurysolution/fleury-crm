<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <div class="settings-icon-badge"><i class="fa-solid fa-diagram-project text-primary fa-lg"></i></div>
        <div>
            <h5 class="fw-bold mb-0">Approval Workflows</h5>
            <small class="text-muted">Define multi-step approval chains for business processes</small>
        </div>
    </div>
    <button type="button" class="btn btn-save" data-bs-toggle="modal" data-bs-target="#workflowModal">
        <i class="fa-solid fa-plus me-2"></i>New Workflow
    </button>
</div>

<?php if (!empty($workflows)): ?>
<div class="row g-3 mb-2">
    <?php $workflows = array_map(fn($w) => (is_object($w) ? (array)$w : $w), $workflows ?? []); foreach($workflows as $wf): ?>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius:12px;border-left:4px solid #4a90e2!important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.9rem;"><?= esc($wf['name']) ?></div>
                        <div class="text-muted" style="font-size:.78rem;"><?= esc($wf['description'] ?? '—') ?></div>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border fw-normal" style="font-size:.72rem;">
                                <?= esc($wf['module'] ?? 'General') ?>
                            </span>
                            <span class="badge fw-normal" style="background:rgba(74,144,226,.12);color:#4a90e2;font-size:.72rem;">
                                <?= count($wf['steps'] ?? []) ?> steps
                            </span>
                            <span class="badge fw-normal <?= ($wf['is_active'] ?? 1) ? 'bg-success' : 'bg-secondary' ?>" style="font-size:.72rem;">
                                <?= ($wf['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary btn-edit-workflow"
                                data-id="<?= $wf['id'] ?>"
                                data-name="<?= esc($wf['name']) ?>"
                                data-description="<?= esc($wf['description'] ?? '') ?>"
                                data-module_key="<?= esc($wf['module_key'] ?? '') ?>"
                                data-is_active="<?= esc($wf['is_active'] ?? 1) ?>"
                                data-bs-toggle="modal" data-bs-target="#workflowModal">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <a href="<?= site_url('settings/approval_workflows/delete/'.$wf['id']) ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this workflow?')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-diagram-project fa-3x mb-3 opacity-25"></i>
    <h6>No Workflows Yet</h6>
    <p class="mb-0 small">Create your first approval workflow to get started.</p>
</div>
<?php endif; ?>

<!-- Workflow Modal -->
<div class="modal fade" id="workflowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#4a90e2,#6f42c1);border-radius:12px 12px 0 0;">
                <h5 class="modal-title text-white" id="workflowModalLabel">New Approval Workflow</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('settings/approval_workflows/save_workflow', ['class'=>'settings-ajax-form']) ?>
                <input type="hidden" name="id" id="workflow_id" value="">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="workflow_name" class="form-label">Workflow Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="workflow_name" class="form-control" required
                                   placeholder="e.g. Purchase Approval">
                        </div>
                        <div class="col-md-6">
                            <label for="workflow_module" class="form-label">Module / Trigger</label>
                            <select name="module_key" id="workflow_module" class="form-select">
                                <option value="">— Any —</option>
                                <option value="invoice">Invoice</option>
                                <option value="estimate">Estimate</option>
                                <option value="contract">Contract</option>
                                <option value="proposal">Proposal</option>
                                <option value="order">Order</option>
                                <option value="expense">Expense</option>
                                <option value="leave">Leave Request</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="workflow_description" class="form-label">Description</label>
                            <textarea name="description" id="workflow_description"
                                      class="form-control" rows="2"
                                      placeholder="What this workflow approves…"></textarea>
                        </div>
                    </div>

                    <div class="settings-section-hdr" style="margin-top:0;">Approval Steps</div>
                    <div id="workflowSteps">
                        <div class="step-row border rounded-3 p-3 mb-2 bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-primary" style="border-radius:50%;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;">1</span>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="steps[0][approver_role]" class="form-control form-control-sm"
                                           placeholder="Approver Role (e.g. Manager)">
                                </div>
                                <div class="col-md-4">
                                    <select name="steps[0][approval_type]" class="form-select form-select-sm">
                                        <option value="any">Any Approver</option>
                                        <option value="all">All Must Approve</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="addStep">
                        <i class="fa-solid fa-plus me-1"></i>Add Step
                    </button>

                    <div class="mt-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                   id="workflow_is_active" value="1" checked>
                            <label class="form-check-label" for="workflow_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-save btn-sm">Save Workflow</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.modal-content{border-radius:12px;}
</style>

<script>
let stepCount = 1;
document.getElementById('addStep').addEventListener('click', function(){
    const idx = stepCount++;
    document.getElementById('workflowSteps').insertAdjacentHTML('beforeend', `
        <div class="step-row border rounded-3 p-3 mb-2 bg-light">
            <div class="row g-2 align-items-center">
                <div class="col-auto"><span class="badge bg-primary" style="border-radius:50%;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;">${idx+1}</span></div>
                <div class="col-md-5"><input type="text" name="steps[${idx}][approver_role]" class="form-control form-control-sm" placeholder="Approver Role"></div>
                <div class="col-md-4"><select name="steps[${idx}][approval_type]" class="form-select form-select-sm">
                    <option value="any">Any Approver</option>
                    <option value="all">All Must Approve</option>
                </select></div>
                <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.step-row').remove()"><i class="fa-solid fa-times"></i></button></div>
            </div>
        </div>`);
});

document.querySelectorAll('.btn-edit-workflow').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.getElementById('workflowModalLabel').textContent = 'Edit Workflow';
        document.getElementById('workflow_id').value = this.dataset.id;
        document.getElementById('workflow_name').value = this.dataset.name;
        document.getElementById('workflow_description').value = this.dataset.description;
        document.getElementById('workflow_module').value = this.dataset.module_key;
        const isActiveChk = document.getElementById('workflow_is_active');
        if(isActiveChk) isActiveChk.checked = (this.dataset.is_active == '1');
    });
});
</script>
<?= $this->endSection() ?>
