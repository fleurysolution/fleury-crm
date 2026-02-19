<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color);">Team Members</h3>
        <p style="color: var(--text-muted);">Manage your team and their roles.</p>
    </div>
    <a href="<?= site_url('team/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus" style="margin-right: 0.5rem;"></i> Add Member
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <tr>
                <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Name</th>
                <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Email</th>
                <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Status</th>
                <th style="text-align: right; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.875rem;">
                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight: 500; color: var(--text-main);"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        </div>
                    </div>
                </td>
                <td style="padding: 1rem; color: var(--text-muted);"><?= esc($user['email']) ?></td>
                <td style="padding: 1rem;">
                    <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; <?= $user['status'] == 'active' ? 'background: #ecfdf5; color: #047857;' : 'background: #fef2f2; color: #b91c1c;' ?>">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </td>
                <td style="padding: 1rem; text-align: right;">
                    <a href="#" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;"><i class="fa-solid fa-pen"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
