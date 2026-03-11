<div class="row">
    <!-- Change Events Section -->
    <div class="col-lg-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Change Events (Potential Changes)</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="fa-solid fa-plus me-1"></i> New Event
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Type</th>
                                <th>Est. Cost</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($change_events)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No change events logged yet.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($change_events as $event): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold"><?= esc($event['title']) ?></div>
                                    <small class="text-muted"><?= esc($event['description']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= ucwords(str_replace('_', ' ', $event['type'])) ?></span></td>
                                <td><?= number_to_currency($event['estimated_cost'], $project['currency'] ?? 'USD') ?></td>
                                <td>
                                    <?php 
                                        $statusClass = match($event['status']) {
                                            'potential' => 'bg-info',
                                            'pending'   => 'bg-warning',
                                            'approved'  => 'bg-success',
                                            'void'      => 'bg-secondary',
                                            default     => 'bg-light text-dark'
                                        };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($event['status']) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($event['status'] !== 'approved'): ?>
                                    <a href="<?= site_url("change-orders/convert/{$event['id']}") ?>" class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-file-export me-1"></i> Convert to CO
                                    </a>
                                    <?php endif; ?>
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

    <!-- Change Orders Section -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Approved / Draft Change Orders</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">CO #</th>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($change_orders)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No change orders issued yet.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($change_orders as $co): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary"><?= esc($co['co_number']) ?></td>
                                <td><?= esc($co['title']) ?></td>
                                <td class="fw-bold"><?= number_to_currency($co['amount'], $project['currency'] ?? 'USD') ?></td>
                                <td>
                                    <?php 
                                        $coStatusClass = match($co['status']) {
                                            'draft'    => 'bg-light text-dark border',
                                            'approved' => 'bg-success',
                                            default    => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?= $coStatusClass ?>"><?= ucfirst($co['status']) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($co['status'] === 'draft'): ?>
                                    <button class="btn btn-sm btn-success" onclick="approveCO(<?= $co['id'] ?>)">Approve</button>
                                    <?php endif; ?>
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

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New Change Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url("change-orders/events/store/{$project['id']}") ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Additional Electrical Outlets">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="owner_change">Owner Directed Change</option>
                            <option value="internal">Internal Change</option>
                            <option value="subcontractor">Subcontractor Change</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Cost Path ($)</label>
                        <input type="number" step="0.01" name="estimated_cost" class="form-control" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveCO(id) {
    if (!confirm('Are you sure you want to approve this Change Order? This will impact the project budget.')) return;
    
    fetch('<?= site_url('change-orders/approve/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
        else alert('Error: ' + d.message);
    });
}
</script>
