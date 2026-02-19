<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="<?= site_url('team') ?>" style="color: var(--text-muted); font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <i class="fa-solid fa-arrow-left"></i> Back to Team
        </a>
        <h3 style="color: var(--primary-color);">Add Team Member</h3>
    </div>

    <div class="card">
        <?= form_open('team/store') ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                            <?= esc($role['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
