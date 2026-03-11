<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Projects</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Projects</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fa-solid fa-filter"></i> Filters
        </button>
        <a href="<?= site_url('projects/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> New Project
        </a>
    </div>
</div>

<!-- Filters Collapse -->
<div class="collapse mb-4" id="filterCollapse">
    <div class="card card-body border-0 shadow-sm">
        <form method="get" action="<?= site_url('projects') ?>" class="row g-3">
            <input type="hidden" name="view" value="<?= esc($viewMode) ?>">
            
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Title, client, etc." value="<?= esc(request()->getGet('search')) ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="all">All Statuses</option>
                    <option value="draft" <?= request()->getGet('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="active" <?= request()->getGet('status') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="on_hold" <?= request()->getGet('status') === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                    <option value="completed" <?= request()->getGet('status') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="archived" <?= request()->getGet('status') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted">Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">All Branches</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= esc($b['id']) ?>" <?= request()->getGet('branch_id') == $b['id'] ? 'selected' : '' ?>><?= esc($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted">Client</label>
                <select name="client_id" class="form-select">
                    <option value="">All Clients</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= esc($c['id']) ?>" <?= request()->getGet('client_id') == $c['id'] ? 'selected' : '' ?>><?= esc($c['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted">Start Date (From)</label>
                <input type="date" name="start_date" class="form-control" value="<?= esc(request()->getGet('start_date')) ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted">End Date (Until)</label>
                <input type="date" name="end_date" class="form-control" value="<?= esc(request()->getGet('end_date')) ?>">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <a href="<?= site_url('projects?view='.esc($viewMode)) ?>" class="btn btn-light">Clear</a>
                <button type="submit" class="btn btn-secondary">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Status Summary & View Toggle -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex gap-2 flex-wrap">
        <?php
        $labels = ['all'=>'All','draft'=>'Draft','active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed','archived'=>'Archived'];
        foreach ($labels as $s => $l):
            $active = ($filter === $s) ? 'btn-primary' : 'btn-outline-secondary';
            $cnt    = $s === 'all' ? array_sum($counts) : ($counts[$s] ?? 0);
            
            // Rebuild URL with current params
            $params = request()->getGet();
            $params['status'] = $s;
        ?>
        <a href="<?= site_url('projects?'.http_build_query($params)) ?>" class="btn btn-sm <?= $active ?>">
            <?= $l ?> <span class="badge bg-white text-dark ms-1"><?= $cnt ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    
    <div class="btn-group btn-group-sm">
        <?php 
            $gridParams = request()->getGet(); $gridParams['view'] = 'grid';
            $listParams = request()->getGet(); $listParams['view'] = 'list';
        ?>
        <a href="<?= site_url('projects?'.http_build_query($gridParams)) ?>" class="btn <?= $viewMode === 'grid' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <i class="fa-solid fa-grid-2"></i> Grid
        </a>
        <a href="<?= site_url('projects?'.http_build_query($listParams)) ?>" class="btn <?= $viewMode === 'list' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <i class="fa-solid fa-list"></i> List
        </a>
    </div>
</div>

<?php if (empty($projects)): ?>
<div class="card border-0 shadow-sm text-center py-5">
    <div class="card-body">
        <i class="fa-solid fa-folder-open fa-3x text-muted mb-3 d-block opacity-25"></i>
        <h6 class="text-muted">No projects found</h6>
        <p class="text-muted small">Try adjusting your filters or create a new project.</p>
        <a href="<?= site_url('projects/create') ?>" class="btn btn-primary mt-2">
            <i class="fa-solid fa-plus me-1"></i> Create Project
        </a>
    </div>
</div>
<?php else: ?>

    <?php if ($viewMode === 'grid'): ?>
    <!-- ── GRID VIEW ── -->
    <div class="row g-3">
    <?php foreach ($projects as $p):
        $statusBadge = [
            'draft'=>'secondary','active'=>'success','on_hold'=>'warning',
            'completed'=>'primary','archived'=>'dark'
        ][$p['status']] ?? 'secondary';
    ?>
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100 project-card" style="border-top: 3px solid <?= esc($p['color']) ?> !important; border-radius: 12px;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <h6 class="fw-bold mb-0">
                        <a href="<?= site_url('projects/'.$p['id']) ?>" class="text-decoration-none text-dark">
                            <?= esc($p['title']) ?>
                        </a>
                    </h6>
                    <span class="badge bg-<?= $statusBadge ?>-subtle text-<?= $statusBadge ?> ms-2">
                        <?= ucfirst(str_replace('_',' ',$p['status'])) ?>
                    </span>
                </div>

                <?php if ($p['client_name']): ?>
                <div class="text-muted small mb-2">
                    <i class="fa-solid fa-building me-1"></i><?= esc($p['client_name']) ?>
                </div>
                <?php endif; ?>

                <?php if ($p['description']): ?>
                <p class="small text-muted mb-3" style="line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    <?= esc($p['description']) ?>
                </p>
                <?php endif; ?>

                <div class="d-flex gap-3 small text-muted mb-3">
                    <?php if ($p['start_date']): ?>
                    <span><i class="fa-regular fa-calendar me-1"></i><?= date('d M Y', strtotime($p['start_date'])) ?></span>
                    <?php endif; ?>
                    <?php if ($p['end_date']): ?>
                    <span><i class="fa-solid fa-flag-checkered me-1"></i><?= date('d M Y', strtotime($p['end_date'])) ?></span>
                    <?php endif; ?>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-auto">
                    <?php if ($p['pm_name']): ?>
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width:28px;height:28px;font-size:.7rem;" title="<?= esc($p['pm_name']) ?>">
                            <?= strtoupper(substr($p['pm_name'],0,1)) ?>
                        </div>
                        <span class="small text-muted"><?= esc($p['pm_name']) ?></span>
                    </div>
                    <?php else: ?><div></div><?php endif; ?>
                    <a href="<?= site_url('projects/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary">
                        Open <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    
    <?php else: ?>
    <!-- ── LIST VIEW ── -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 text-muted small py-3 px-4">Project</th>
                        <th class="border-0 text-muted small py-3">Client</th>
                        <th class="border-0 text-muted small py-3">PM</th>
                        <th class="border-0 text-muted small py-3">Stage & Contract</th>
                        <th class="border-0 text-muted small py-3">Timeline</th>
                        <th class="border-0 text-muted small py-3">Status</th>
                        <th class="border-0 text-muted small py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p):
                        $statusBadge = [
                            'draft'=>'secondary','active'=>'success','on_hold'=>'warning',
                            'completed'=>'primary','archived'=>'dark'
                        ][$p['status']] ?? 'secondary';
                    ?>
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?= esc($p['color']) ?>;"></div>
                                <div>
                                    <a href="<?= site_url('projects/'.$p['id']) ?>" class="text-decoration-none text-dark fw-bold d-block">
                                        <?= esc($p['title']) ?>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($p['client_name']): ?>
                                <span class="text-dark small"><i class="fa-solid fa-building text-muted me-1"></i><?= esc($p['client_name']) ?></span>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['pm_name']): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width:24px;height:24px;font-size:.6rem;" title="<?= esc($p['pm_name']) ?>">
                                        <?= strtoupper(substr($p['pm_name'],0,1)) ?>
                                    </div>
                                    <span class="small text-muted"><?= esc($p['pm_name']) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small">
                                <div><span class="text-muted">Stage:</span> <?= ucwords(str_replace('_', ' ', $p['project_stage'] ?? '')) ?></div>
                                <?php if($p['contract_type']): ?>
                                <div><span class="text-muted">Type:</span> <?= ucwords(str_replace('_', ' ', $p['contract_type'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="small text-muted">
                                <?php if ($p['start_date']): ?><div><i class="fa-regular fa-calendar me-1"></i><?= date('d M Y', strtotime($p['start_date'])) ?></div><?php endif; ?>
                                <?php if ($p['end_date']): ?><div><i class="fa-solid fa-flag-checkered me-1"></i><?= date('d M Y', strtotime($p['end_date'])) ?></div><?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?= $statusBadge ?>-subtle text-<?= $statusBadge ?>">
                                <?= ucfirst(str_replace('_',' ',$p['status'])) ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?= site_url('projects/'.$p['id']) ?>" class="btn btn-sm btn-light fs-12">
                                Open <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>
