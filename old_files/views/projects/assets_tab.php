
<div class="container">
    <h4>Project Assets </h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <a href="<?= base_url('/projects/assign_asset_form/' . $project_id) ?>" class="btn btn-primary mb-3">
        Assign Asset
    </a>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Asset</th>
                <th>Series</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Assigned Date</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($assignments)): $i = 1; foreach ($assignments as $a): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= esc($a['asset_name'] ?? 'N/A'); ?></td>
                <td><?= esc($a['series'] ?? '-'); ?></td>
                <td><?= esc($a['quantity']); ?></td>
                <td><?= esc($a['unit_title'] ?? ''); ?></td>
                <td><?= date('d M Y H:i', strtotime($a['assigned_date'])); ?></td>
                <td><?= esc(ucfirst($a['status'])); ?></td>
                <td><?= esc($a['remarks']); ?></td>
                <td>
                    <?php if ($a['status'] === 'assigned'): ?>
                        <a href="<?= base_url('/projects/return_asset/' . $a['id']) ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Mark this assignment as returned?');">Return</a>
                    <?php else: ?>
                        <span class="badge badge-secondary">Returned</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="9">No assets assigned to this project.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
