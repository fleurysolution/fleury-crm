<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Client Permissions</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="client">

<div class="row">

    <div class="col-md-12 mb-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="client_can_create_projects" name="client_can_create_projects" value="1" <?= setting('client_can_create_projects') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="client_can_create_projects">Clients can create projects?</label>
                </div>
                
                 <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="client_can_view_tasks" name="client_can_view_tasks" value="1" <?= setting('client_can_view_tasks') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="client_can_view_tasks">Clients can view tasks?</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="client_can_create_tasks" name="client_can_create_tasks" value="1" <?= setting('client_can_create_tasks') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="client_can_create_tasks">Clients can create tasks?</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="client_can_edit_tasks" name="client_can_edit_tasks" value="1" <?= setting('client_can_edit_tasks') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="client_can_edit_tasks">Clients can edit tasks?</label>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="client_can_comment_on_tasks" name="client_can_comment_on_tasks" value="1" <?= setting('client_can_comment_on_tasks') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="client_can_comment_on_tasks">Clients can comment on tasks?</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label">Disable Client Login</label>
        <select name="disable_client_login" class="form-select">
             <option value="0" <?= setting('disable_client_login') == '0' ? 'selected' : '' ?>>No</option>
            <option value="1" <?= setting('disable_client_login') == '1' ? 'selected' : '' ?>>Yes</option>
        </select>
    </div>
    
     <div class="col-md-12 mb-3">
        <label class="form-label">Disable Client Signup</label>
        <select name="disable_client_signup" class="form-select">
             <option value="0" <?= setting('disable_client_signup') == '0' ? 'selected' : '' ?>>No</option>
            <option value="1" <?= setting('disable_client_signup') == '1' ? 'selected' : '' ?>>Yes</option>
        </select>
    </div>
    
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
