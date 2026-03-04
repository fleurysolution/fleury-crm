<?php
$poModel = new \App\Models\PurchaseOrderModel();
$pos = $poModel->forProject($project['id']);
$subcontractors = (new \App\Models\UserModel())->select('fs_users.*')
    ->join('user_roles', 'user_roles.user_id = fs_users.id')
    ->join('roles', 'roles.id = user_roles.role_id')
    ->where('roles.slug', 'subcontractor_vendor')
    ->findAll(); 
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Purchase Orders</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newPoModal">
        <i class="fa-solid fa-plus me-1"></i> Draft Purchase Order
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">PO Number</th>
                    <th>Title</th>
                    <th>Subcontractor / Vendor</th>
                    <th>Delivery Date</th>
                    <th>Amount</th>
                    <th class="pe-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pos)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-file-contract fs-3 d-block mb-3"></i>
                            No Purchase Orders drafted yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pos as $po): ?>
                        <tr>
                            <td class="ps-3 fw-bold">
                                <a href="<?= site_url("procurement/pos/{$po['id']}") ?>" class="text-decoration-none">
                                    <?= esc($po['po_number']) ?>
                                </a>
                            </td>
                            <td class="fw-medium"><?= esc($po['title']) ?></td>
                            <td>
                                <?php if ($po['vendor_name']): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-secondary text-white" style="width: 24px; height: 24px; font-size: 0.7rem;"><?= substr($po['vendor_name'], 0, 1) ?></div>
                                        <div>
                                            <span class="d-block small fw-bold"><?= esc($po['vendor_name']) ?></span>
                                            <span class="d-block small text-muted" style="font-size: 0.7rem;">Vendor Account</span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small border px-2 py-1 rounded">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= $po['delivery_date'] ? date('M d, Y', strtotime($po['delivery_date'])) : 'TBD' ?>
                            </td>
                            <td class="fw-bold">
                                $<?= number_format($po['total_amount'], 2) ?>
                            </td>
                            <td class="pe-3">
                                <?php 
                                    $badge = 'bg-secondary';
                                    if ($po['status'] === 'Sent') $badge = 'bg-primary';
                                    if ($po['status'] === 'Executed') $badge = 'bg-success';
                                    if ($po['status'] === 'Void') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?>"><?= esc($po['status']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Draft PO Modal -->
<div class="modal fade" id="newPoModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/procurement/pos") ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Draft Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">PO Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Framing Subcontract" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">Select Subcontractor / Vendor</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">-- To Be Determined --</option>
                        <?php foreach ($subcontractors as $sub): ?>
                            <option value="<?= $sub['id'] ?>"><?= esc($sub['first_name'] . ' ' . $sub['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Required Delivery / Start Date</label>
                    <input type="date" name="delivery_date" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    Start Draft <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
