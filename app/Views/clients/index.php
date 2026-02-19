<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="m-0">Clients</h3>
            <p class="text-muted m-0" style="font-size: 0.875rem;">Manage your client relationships</p>
        </div>
        <!-- <a href="#" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Client</a> -->
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Primary Contact</th>
                    <th>Status</th>
                    <th>Projects</th>
                    <th>Due</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="6" class="text-center p-4">
                        <div class="text-muted">No clients found. Convert a lead to get started.</div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td>
                            <div class="font-bold text-dark"><?= esc($client['company_name']) ?></div>
                             <?php if ($client['city']): ?>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-location-dot mr-1"></i> <?= esc($client['city']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="text-muted">-</div>
                        </td>
                        <td>
                            <span class="badge badge-success">Active</span>
                        </td>
                        <td>0</td>
                        <td>$0.00</td>
                        <td class="text-right">
                            <a href="<?= site_url('clients/' . $client['id']) ?>" class="btn btn-sm btn-secondary">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
