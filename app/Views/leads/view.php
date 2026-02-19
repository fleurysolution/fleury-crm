<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Highlights Panel -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(to right, #f8f9fa, #ffffff); border-left: 5px solid var(--primary-color) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-lg rounded bg-white shadow-sm d-flex align-items-center justify-content-center me-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-bullseye fa-2x text-primary"></i>
                </div>
                <div>
                    <h6 class="text-uppercase text-muted small mb-1">Lead</h6>
                    <h2 class="mb-0 fw-bold text-dark"><?= esc($lead['company_name']) ?></h2>
                    <p class="mb-0 text-muted"><i class="fa-solid fa-user me-1"></i> <?= esc($lead['title']) ?></p>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                 <?php if($lead['status'] !== 'won'): ?>
                    <a href="<?= site_url('leads/convert/' . $lead['id']) ?>" class="btn btn-success" onclick="return confirm('Convert this lead to a client?')">
                        <i class="fa-solid fa-check me-2"></i> Convert
                    </a>
                <?php endif; ?>
                <a href="<?= site_url('leads/edit/' . $lead['id']) ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
            </div>
        </div>
        
        <div class="row mt-4 g-3">
             <div class="col-auto px-4 border-end">
                <div class="small text-muted text-uppercase fw-bold mb-1">Status</div>
                 <span class="badge bg-<?= $lead['status'] == 'new' ? 'primary' : ($lead['status'] == 'won' ? 'success' : 'warning') ?> fs-6 rounded-pill px-3">
                    <?= ucfirst($lead['status']) ?>
                </span>
            </div>
            <div class="col-auto px-4 border-end">
                 <div class="small text-muted text-uppercase fw-bold mb-1">Source</div>
                <div class="h5 mb-0"><?= esc($lead['source'] ?: '-') ?></div>
            </div>
            <div class="col-auto px-4 border-end">
                 <div class="small text-muted text-uppercase fw-bold mb-1">Phone</div>
                <div class="h5 mb-0"><?= esc($lead['phone'] ?: '-') ?></div>
            </div>
             <div class="col-auto px-4">
                 <div class="small text-muted text-uppercase fw-bold mb-1">Email</div>
                <div class="h5 mb-0"><?= esc($lead['email'] ?: '-') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs nav-tabs-custom mb-4" id="leadTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab == 'overview' ? 'active' : '' ?>" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" data-url="<?= site_url('leads/overview/' . $lead['id']) ?>">Overview</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab == 'contacts' ? 'active' : '' ?>" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" type="button" role="tab" data-url="<?= site_url('leads/contacts/' . $lead['id']) ?>">Contacts</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab == 'tasks' ? 'active' : '' ?>" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" data-url="<?= site_url('leads/tasks/' . $lead['id']) ?>">Tasks</button>
    </li>
    <li class="nav-item" role="presentation">
         <button class="nav-link <?= $active_tab == 'notes' ? 'active' : '' ?>" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" data-url="<?= site_url('leads/notes/' . $lead['id']) ?>">Notes</button>
    </li>
     <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab == 'files' ? 'active' : '' ?>" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab" data-url="<?= site_url('leads/files/' . $lead['id']) ?>">Files</button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="leadTabsContent">
    <div class="tab-pane fade <?= $active_tab == 'overview' ? 'show active' : '' ?>" id="overview" role="tabpanel">
        <?php if($active_tab == 'overview') echo view_cell('App\Controllers\Leads::overview', ['id' => $lead['id']]); ?>
    </div>
    <div class="tab-pane fade <?= $active_tab == 'contacts' ? 'show active' : '' ?>" id="contacts" role="tabpanel">
         <?php if($active_tab == 'contacts') echo view_cell('App\Controllers\Leads::contacts', ['id' => $lead['id']]); ?>
    </div>
    <div class="tab-pane fade <?= $active_tab == 'tasks' ? 'show active' : '' ?>" id="tasks" role="tabpanel">
         <?php if($active_tab == 'tasks') echo view_cell('App\Controllers\Leads::tasks', ['id' => $lead['id']]); ?>
    </div>
     <div class="tab-pane fade <?= $active_tab == 'notes' ? 'show active' : '' ?>" id="notes" role="tabpanel">
         <?php if($active_tab == 'notes') echo view_cell('App\Controllers\Leads::notes', ['id' => $lead['id']]); ?>
    </div>
     <div class="tab-pane fade <?= $active_tab == 'files' ? 'show active' : '' ?>" id="files" role="tabpanel">
         <?php if($active_tab == 'files') echo view_cell('App\Controllers\Leads::files', ['id' => $lead['id']]); ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var triggerTabList = [].slice.call(document.querySelectorAll('#leadTabs button'))
        triggerTabList.forEach(function(triggerEl) {
            triggerEl.addEventListener('show.bs.tab', function(event) {
                var targetId = event.target.getAttribute('data-bs-target');
                var url = event.target.getAttribute('data-url');
                var targetPane = document.querySelector(targetId);
                
                if(targetPane.innerHTML.trim() == '') {
                    targetPane.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            targetPane.innerHTML = html;
                        });
                }
            })
        })
    });
</script>

<style>
.nav-tabs-custom {
    border-bottom: 2px solid #e9ecef;
}
.nav-tabs-custom .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    color: #6c757d;
    padding: 1rem 1.5rem;
    font-weight: 500;
}
.nav-tabs-custom .nav-link:hover {
    color: var(--primary-color);
    border-color: transparent;
}
.nav-tabs-custom .nav-link.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
    background: transparent;
}
</style>

<?= $this->endSection() ?>
