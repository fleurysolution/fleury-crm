<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color);">Roles & Permissions</h3>
        <p style="color: var(--text-muted);">Manage different user roles and their access levels.</p>
    </div>
    <a href="<?= site_url('roles/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus" style="margin-right: 0.5rem;"></i> Create Role
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <tr>
                <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Role Name</th>
                <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Slug</th>
                <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Description</th>
                <th style="text-align: right; padding: 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.875rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem; font-weight: 500;"><?= esc($role['name']) ?></td>
                <td style="padding: 1rem; color: var(--text-muted);"><span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px; font-family: monospace; font-size: 0.8rem;"><?= esc($role['slug']) ?></span></td>
                <td style="padding: 1rem; color: var(--text-muted);"><?= esc($role['description']) ?></td>
                <td style="padding: 1rem; text-align: right;">
                    <a href="#" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;"><i class="fa-solid fa-pen"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
