<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="row align-items-center mb-4">
    <div class="col-auto">
         <a href="<?= site_url('clients/' . $client_info->id) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
    <div class="col">
        <h1 class="h3 mb-0"><?= esc($user_info->first_name . " " . $user_info->last_name) ?></h1>
        <p class="text-muted mb-0">Contact Profile</p>
    </div>
</div>

<div class="row">
    <!-- Sidebar / Profile Card -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                 <div class="avatar-xl rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 96px; height: 96px; font-size: 2.5rem; font-weight: 600;">
                      <?= strtoupper(substr($user_info->first_name, 0, 1) . substr($user_info->last_name, 0, 1)) ?>
                 </div>
                 <h4 class="mb-1"><?= esc($user_info->first_name . " " . $user_info->last_name) ?></h4>
                 <p class="text-muted mb-3"><?= esc($user_info->job_title) ?: 'No Job Title' ?></p>
                 
                 <div class="d-grid gap-2">
                     <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-envelope me-2"></i> Send Email</button>
                 </div>
            </div>
             <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex align-items-center bg-transparent px-4">
                    <i class="fa-regular fa-envelope text-muted me-3 width-20"></i> <?= esc($user_info->email) ?>
                </li>
                <li class="list-group-item d-flex align-items-center bg-transparent px-4">
                    <i class="fa-solid fa-phone text-muted me-3 width-20"></i> <?= esc($user_info->phone) ?: 'No Phone' ?>
                </li>
                 <li class="list-group-item d-flex align-items-center bg-transparent px-4">
                    <i class="fa-regular fa-building text-muted me-3 width-20"></i> <?= esc($client_info->company_name) ?>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content with Tabs -->
    <div class="col-md-9">
         <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom px-4">
                 <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'general' ? 'active' : '' ?>" href="<?= site_url('clients/contact_profile/' . $user_info->id . '/general') ?>">General Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'account' ? 'active' : '' ?>" href="<?= site_url('clients/contact_profile/' . $user_info->id . '/account') ?>">Account Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'social' ? 'active' : '' ?>" href="<?= site_url('clients/contact_profile/' . $user_info->id . '/social') ?>">Social Links</a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                
                <?php if ($tab == 'general'): ?>
                    <?= form_open(site_url("clients/save_contact"), ["class" => "general-form"]) ?>
                    <input type="hidden" name="contact_id" value="<?= $user_info->id ?>" />
                    <input type="hidden" name="client_id" value="<?= $user_info->client_id ?>" />
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" value="<?= esc($user_info->first_name) ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" value="<?= esc($user_info->last_name) ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" value="<?= esc($user_info->email) ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                             <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="<?= esc($user_info->phone) ?>" class="form-control">
                        </div>
                        <div class="col-md-12">
                             <label class="form-label">Job Title</label>
                            <input type="text" name="job_title" value="<?= esc($user_info->job_title) ?>" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                    <?= form_close() ?>

                <?php elseif ($tab == 'account'): ?>
                    <?= form_open(site_url("clients/save_contact"), ["class" => "general-form"]) ?>
                    <input type="hidden" name="contact_id" value="<?= $user_info->id ?>" />
                     <input type="hidden" name="client_id" value="<?= $user_info->client_id ?>" />
                    
                    <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Leaving the password field blank will keep the current password unchanged.
                    </div>

                    <div class="row g-3">
                         <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                             <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirm" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                    <?= form_close() ?>
                    
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-person-digging fa-2x mb-3 opacity-50"></i>
                        <p>This tab is under construction.</p>
                    </div>
                <?php endif; ?>
                
            </div>
         </div>
    </div>
</div>

<style>
.width-20 { width: 20px; text-align: center; }
</style>

<?= $this->endSection() ?>
