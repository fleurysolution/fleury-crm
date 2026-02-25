<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <div class="settings-icon-badge"><i class="fa-solid fa-user-shield text-primary fa-lg"></i></div>
        <div>
            <h5 class="fw-bold mb-0">Roles & Permissions</h5>
            <small class="text-muted">Manage roles and their access permissions</small>
        </div>
    </div>
    <button type="button" class="btn btn-save" data-bs-toggle="modal" data-bs-target="#roleModal">
        <i class="fa-solid fa-plus me-2"></i>New Role
    </button>
</div>

<!-- Roles Table -->
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="rolesTable">
        <thead style="background:#f8f9fb;">
            <tr>
                <th class="border-0 text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Role</th>
                <th class="border-0 text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Slug</th>
                <th class="border-0 text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Permissions</th>
                <th class="border-0 text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Users</th>
                <th class="border-0 text-muted fw-semibold text-end" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($roles)): ?>
            <?php foreach ($roles as $role): ?>
            <tr>
                <td>
                    <div class="fw-semibold" style="font-size:.875rem;"><?= esc($role['name']) ?></div>
                    <div class="text-muted" style="font-size:.75rem;"><?= esc($role['description'] ?? '—') ?></div>
                </td>
                <td><span class="badge bg-light text-dark border fw-normal"><?= esc($role['slug']) ?></span></td>
                <td>
                    <span class="badge" style="background:rgba(74,144,226,.15);color:#4a90e2;">
                        <?= $role['permissions_count'] ?? count($role['permissions'] ?? []) ?> permissions
                    </span>
                </td>
                <td><span class="badge bg-light text-muted border"><?= $role['users_count'] ?? '—' ?></span></td>
                <td class="text-end">
                    <div class="d-flex gap-1 justify-content-end">
                        <button class="btn btn-sm btn-outline-primary btn-edit-role"
                                data-id="<?= $role['id'] ?>"
                                data-name="<?= esc($role['name']) ?>"
                                data-slug="<?= esc($role['slug']) ?>"
                                data-description="<?= esc($role['description'] ?? '') ?>"
                                data-bs-toggle="modal" data-bs-target="#roleModal">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <?php if(empty($role['is_system'])): ?>
                        <a href="<?= site_url('settings/rbac/delete/'.$role['id']) ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete role <?= esc($role['name']) ?>?')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fa-solid fa-user-shield fa-2x mb-2 opacity-25"></i>
                    <p class="mb-0">No roles found. Click <strong>New Role</strong> to create one.</p>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Permissions Matrix (collapse) -->
<?php if (!empty($permissions)): ?>
<div class="mt-4">
    <button class="btn btn-sm btn-outline-secondary" type="button"
            data-bs-toggle="collapse" data-bs-target="#permissionsMatrix">
        <i class="fa-solid fa-table me-1"></i>View All Permissions (<?= count($permissions) ?>)
    </button>
    <div class="collapse mt-3" id="permissionsMatrix">
        <div class="row g-2">
        <?php foreach($permissions as $perm): ?>
            <div class="col-md-4 col-sm-6">
                <div class="border rounded-3 px-3 py-2 bg-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-key text-muted" style="font-size:.75rem;"></i>
                    <div>
                        <div class="fw-semibold" style="font-size:.8rem;"><?= esc($perm['name']) ?></div>
                        <div class="text-muted" style="font-size:.72rem;"><?= esc($perm['slug']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#4a90e2,#6f42c1);border-radius:12px 12px 0 0;">
                <h5 class="modal-title text-white" id="roleModalLabel">New Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('settings/rbac/save_role', ['class'=>'settings-ajax-form']) ?>
                <input type="hidden" name="id" id="role_id" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="role_name" class="form-control" required
                               placeholder="e.g. Manager">
                    </div>
                    <div class="mb-3">
                        <label for="role_slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="role_slug" class="form-control"
                               placeholder="e.g. manager" pattern="[a-z0-9_-]+" required>
                        <div class="form-text">Lowercase, letters/numbers/underscores only.</div>
                    </div>
                    <div class="mb-3">
                        <label for="role_description" class="form-label">Description</label>
                        <textarea name="description" id="role_description" class="form-control" rows="2"></textarea>
                    </div>
                    <?php if (!empty($permissions)): ?>
                    <div class="mb-1">
                        <label class="form-label">Assign Permissions</label>
                        <div class="border rounded-3 p-3" style="max-height:220px;overflow-y:auto;">
                            <?php foreach($permissions as $perm): ?>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox"
                                       name="permissions[]" value="<?= $perm['id'] ?>"
                                       id="perm_<?= $perm['id'] ?>">
                                <label class="form-check-label" for="perm_<?= $perm['id'] ?>"
                                       style="font-size:.855rem;"><?= esc($perm['name']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-save btn-sm">Save Role</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.table th, .table td{padding:.85rem 1rem;font-size:.875rem;}
.modal-content{border-radius:12px;}
</style>

<script>
// Pre-fill modal for edit
document.querySelectorAll('.btn-edit-role').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.getElementById('roleModalLabel').textContent = 'Edit Role';
        document.getElementById('role_id').value  = this.dataset.id;
        document.getElementById('role_name').value = this.dataset.name;
        document.getElementById('role_slug').value = this.dataset.slug;
        document.getElementById('role_description').value = this.dataset.description;
    });
});
// Reset on new
document.querySelector('[data-bs-target="#roleModal"]:not(.btn-edit-role)') &&
document.querySelector('[data-bs-target="#roleModal"]:not(.btn-edit-role)').addEventListener('click', function(){
    document.getElementById('roleModalLabel').textContent = 'New Role';
    document.getElementById('role_id').value = '';
    document.getElementById('role_name').value = '';
    document.getElementById('role_slug').value = '';
    document.getElementById('role_description').value = '';
    document.querySelectorAll('[name="permissions[]"]').forEach(c => c.checked = false);
});
// Auto-generate slug from name
document.getElementById('role_name').addEventListener('input', function(){
    const slugField = document.getElementById('role_slug');
    if (!slugField.dataset.manual) {
        slugField.value = this.value.toLowerCase().replace(/[^a-z0-9]/g,'_');
    }
});
document.getElementById('role_slug').addEventListener('input', function(){
    this.dataset.manual = 'yes';
});
</script>
<?= $this->endSection() ?>
