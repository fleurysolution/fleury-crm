<?= $this->extend('layouts/vendor_dashboard'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Vendor Dashboard</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-xl-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="mt-0 font-weight-normal">Pending Bids</h4>
                <h2 class="mt-3 mb-0"><?= count($pending_bids) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h4 class="mt-0 font-weight-normal">Active POs</h4>
                <h2 class="mt-3 mb-0"><?= count($active_pos) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h4 class="mt-0 font-weight-normal">Assigned Tasks</h4>
                <h2 class="mt-3 mb-0"><?= count($assigned_tasks) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Recent Purchase Orders</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date Issued</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($active_pos)): ?>
                                <?php foreach (array_slice($active_pos, 0, 5) as $po): ?>
                                    <tr>
                                        <td><?= esc($po['po_number']) ?></td>
                                        <td>$<?= number_format((float) $po['total_amount'], 2) ?></td>
                                        <td><span class="badge bg-success"><?= esc($po['status']) ?></span></td>
                                        <td><?= date('M j, Y', strtotime((string)$po['created_at'])) ?></td>
                                        <td>
                                            <!-- Vendors download PO PDF from their side -->
                                            <a href="<?= site_url('procurement/pos/' . $po['id'] . '/pdf') ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fa-solid fa-download"></i> PDF</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No recent POs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Tasks & To-Dos</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($assigned_tasks)): ?>
                                <?php foreach (array_slice($assigned_tasks, 0, 5) as $task): ?>
                                    <tr>
                                        <td><?= esc($task['title']) ?></td>
                                        <td><?= $task['due_date'] ? date('M j, Y', strtotime((string)$task['due_date'])) : '<span class="text-muted">No Date</span>' ?></td>
                                        <td>
                                            <?php if ($task['status'] === 'done'): ?>
                                                <span class="badge bg-success">Done</span>
                                            <?php elseif ($task['status'] === 'in_progress'): ?>
                                                <span class="badge bg-info">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">To Do</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No assigned tasks found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
