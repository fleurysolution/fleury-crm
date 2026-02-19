<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Highlights Panel (Salesforce Style) -->
<div class="card mb-4 border-top-primary">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-lg bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; font-size: 24px;">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold"><?= esc($client['company_name']) ?></h2>
                    <div class="text-muted small">
                        <?php if ($client['website']): ?>
                            <a href="<?= esc($client['website']) ?>" target="_blank" class="text-muted me-3"><i class="fa-solid fa-globe me-1"></i> Website</a>
                        <?php endif; ?>
                        <?php if ($client['phone']): ?>
                            <span class="me-3"><i class="fa-solid fa-phone me-1"></i> <?= esc($client['phone']) ?></span>
                        <?php endif; ?>
                        <span class="badge bg-<?= esc($client['status_color']) ?>"><?= ucfirst(esc($client['status'])) ?></span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline"><i class="fa-solid fa-pen"></i> Edit</button>
                <button class="btn btn-outline"><i class="fa-solid fa-envelope"></i> Email</button>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Deal</button>
            </div>
        </div>
        
        <hr class="my-4 op-1">

        <div class="row g-4 text-center text-md-start">
            <div class="col-6 col-md-2">
                <div class="text-muted small text-uppercase fw-bold mb-1">Total Due</div>
                <div class="h5 mb-0 text-dark"><?= $client['currency_symbol'] ?><?= number_format($client['due'], 2) ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-muted small text-uppercase fw-bold mb-1">Invoiced</div>
                <div class="h5 mb-0 text-dark"><?= $client['currency_symbol'] ?><?= number_format($client['invoice_value'], 2) ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-muted small text-uppercase fw-bold mb-1">Paid</div>
                <div class="h5 mb-0 text-success"><?= $client['currency_symbol'] ?><?= number_format($client['payment_received'], 2) ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-muted small text-uppercase fw-bold mb-1">Open Projects</div>
                <div class="h5 mb-0 text-dark"><?= esc($client['total_projects']) ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-muted small text-uppercase fw-bold mb-1">Owner</div>
                <div class="d-flex align-items-center gap-2">
                   <!-- <div class="avatar-xs bg-secondary rounded-circle" style="width: 20px; height: 20px;"></div> -->
                    <span>Me</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="card">
    <div class="card-header border-bottom-0 pb-0">
        <ul class="nav nav-tabs card-header-tabs" id="clientTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contacts-tab" data-bs-toggle="tab" href="#contacts" role="tab">Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="projects-tab" data-bs-toggle="tab" href="#projects" role="tab">Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="invoices-tab" data-bs-toggle="tab" href="#invoices" role="tab">Invoices</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="estimates-tab" data-bs-toggle="tab" href="#estimates" role="tab">Estimates</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tickets-tab" data-bs-toggle="tab" href="#tickets" role="tab">Tickets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="files-tab" data-bs-toggle="tab" href="#files" role="tab">Files</a>
            </li>
             <li class="nav-item">
                <a class="nav-link" id="notes-tab" data-bs-toggle="tab" href="#notes" role="tab">Notes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="expenses-tab" data-bs-toggle="tab" href="#expenses" role="tab">Expenses</a>
            </li>
             <li class="nav-item">
                <a class="nav-link" id="events-tab" data-bs-toggle="tab" href="#events" role="tab">Events</a>
            </li>
             <li class="nav-item">
                <a class="nav-link" id="contracts-tab" data-bs-toggle="tab" href="#contracts" role="tab">Contracts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="proposals-tab" data-bs-toggle="tab" href="#proposals" role="tab">Proposals</a>
            </li>
            
        </ul>
    </div>
    <div class="card-body bg-light">
        <div class="tab-content" id="clientTabsContent">
            <!-- Dynamic Tab Content Loading -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <?= view_cell('App\Controllers\Clients::overview', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="contacts" role="tabpanel">
                <?= view_cell('App\Controllers\Clients::contacts', ['id' => $client['id']]) ?>
            </div>

            <div class="tab-pane fade" id="projects" role="tabpanel">
                <?= view_cell('App\Controllers\Clients::projects', ['id' => $client['id']]) ?>
            </div>

            <div class="tab-pane fade" id="invoices" role="tabpanel">
                <?= view_cell('App\Controllers\Clients::invoices', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="estimates" role="tabpanel">
                <?= view_cell('App\Controllers\Clients::estimates', ['id' => $client['id']]) ?>
            </div>

            <div class="tab-pane fade" id="tickets" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::tickets', ['id' => $client['id']]) ?>
            </div>

            <div class="tab-pane fade" id="files" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::files', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="notes" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::notes', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="expenses" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::expenses', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="events" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::events', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="contracts" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::contracts', ['id' => $client['id']]) ?>
            </div>
            
            <div class="tab-pane fade" id="proposals" role="tabpanel">
                 <?= view_cell('App\Controllers\Clients::proposals', ['id' => $client['id']]) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
