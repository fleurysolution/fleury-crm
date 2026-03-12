<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Long-Lead Material Tracking</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('projects/'.$project['id']) ?>"><?= $project['name'] ?></a></li>
                    <li class="breadcrumb-item active">Procurement</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" onclick="openNewItemModal()">
            <i class="fa-solid fa-plus me-1"></i>Add Material Item
        </button>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Material / Equipment</th>
                                    <th>Linked Task</th>
                                    <th>Status</th>
                                    <th>Lead Time</th>
                                    <th>Expected On-Site</th>
                                    <th>Tracking / Notes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($items)): ?>
                                    <tr><td colspan="7" class="text-center py-5 text-muted">No material items tracked yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach($items as $item): ?>
                                        <tr>
                                            <td class="fw-bold"><?= $item['item_name'] ?></td>
                                            <td>
                                                <?php 
                                                    $linkedTask = array_filter($tasks, fn($t) => $t['id'] == $item['task_id']);
                                                    $task = reset($linkedTask);
                                                    echo $task ? $task['name'] : '<span class="text-muted">None</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $item['status'] == 'delayed' ? 'danger' : 
                                                    ($item['status'] == 'delivered' ? 'success' : 'primary') 
                                                ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= $item['lead_time_days'] ?> Days</td>
                                            <td>
                                                <span class="<?= $item['status'] == 'delayed' ? 'text-danger fw-bold' : '' ?>">
                                                    <?= date('M d, Y', strtotime($item['expected_on_site'])) ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?= $item['notes'] ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-icon btn-light" onclick='editItem(<?= json_encode($item) ?>)'>
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="itemForm">
                <input type="hidden" name="id" id="itemId">
                <div class="modal-header">
                    <h5 class="modal-title">Procurement Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Material Name</label>
                        <input type="text" name="item_name" id="itemName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Linked Activity (Schedule)</label>
                        <select name="task_id" id="task_id" class="form-select">
                            <option value="">- No Link -</option>
                            <?php foreach($tasks as $task): ?>
                                <option value="<?= $task['id'] ?>"><?= $task['activity_id'] ?>: <?= $task['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">The schedule will automatically adjust if this material is delayed.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lead Time (Days)</label>
                            <input type="number" name="lead_time_days" id="leadTime" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expected Date</label>
                            <input type="date" name="expected_on_site" id="expectedDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="itemStatus" class="form-select">
                            <option value="not_ordered">Not Ordered</option>
                            <option value="ordered">Ordered</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="delayed">Delayed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="itemNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save & Recalculate Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = new bootstrap.Modal(document.getElementById('itemModal'));

    function openNewItemModal() {
        document.getElementById('itemForm').reset();
        document.getElementById('itemId').value = '';
        modal.show();
    }

    function editItem(item) {
        document.getElementById('itemId').value = item.id;
        document.getElementById('itemName').value = item.item_name;
        document.getElementById('task_id').value = item.task_id;
        document.getElementById('leadTime').value = item.lead_time_days;
        document.getElementById('expectedDate').value = item.expected_on_site;
        document.getElementById('itemStatus').value = item.status;
        document.getElementById('itemNotes').value = item.notes;
        modal.show();
    }

    document.getElementById('itemForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch(`<?= base_url('projects/'.$project['id'].'/procurement/save') ?>`, {
            method: 'POST',
            body: fd
        }).then(r => r.json()).then(res => {
            if(res.success) {
                location.reload();
            }
        });
    });
</script>
<?php $this->endSection(); ?>
