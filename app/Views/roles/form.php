<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="<?= site_url('roles') ?>" style="color: var(--text-muted); font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <i class="fa-solid fa-arrow-left"></i> Back to Roles
        </a>
        <h3 style="color: var(--primary-color);">Create New Role</h3>
    </div>

    <div class="card">
        <?= form_open('roles/store') ?>
            <div class="form-group">
                <label class="form-label">Role Name</label>
                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required placeholder="e.g. Sales Manager">
            </div>

            <div class="form-group">
                <label class="form-label">Role Slug</label>
                <input type="text" name="slug" class="form-control" value="<?= old('slug') ?>" required placeholder="e.g. sales_manager">
                <small style="color: var(--text-muted);">Unique identifier for the role. Used in code checks.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" style="margin-bottom: 1rem;">Permissions</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                    <?php foreach ($permissions as $perm): ?>
                        <div style="display: flex; gap: 0.5rem; align-items: flex-start; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" id="perm_<?= $perm['id'] ?>" style="margin-top: 0.25rem;">
                            <label for="perm_<?= $perm['id'] ?>" style="cursor: pointer;">
                                <div style="font-weight: 500; font-size: 0.9rem;"><?= esc($perm['name']) ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($perm['description']) ?></div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="btn btn-primary">Create Role</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
