<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Lead Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Company Name</div>
                    <div class="col-md-8"><?= esc($lead['company_name']) ?></div>
                </div>
                 <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Title</div>
                    <div class="col-md-8"><?= esc($lead['title']) ?></div>
                </div>
                 <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Address</div>
                    <div class="col-md-8">
                        <?= esc($lead['address']) ?><br>
                        <?= esc($lead['city']) ?>, <?= esc($lead['state']) ?> <?= esc($lead['zip']) ?><br>
                        <?= esc($lead['country']) ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Status</div>
                    <div class="col-md-8">
                        <span class="badge badge-<?= $lead['status'] == 'won' ? 'success' : ($lead['status'] == 'new' ? 'primary' : 'warning') ?>"><?= ucfirst($lead['status']) ?></span>
                    </div>
                </div>
                 <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Source</div>
                    <div class="col-md-8"><?= esc($lead['source']) ?></div>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
             <div class="card-header">
                <h5 class="card-title mb-0">Contacts</h5>
            </div>
            <div class="card-body">
                 <!-- Simple list or link to contacts tab -->
                 <p class="text-muted">View Contacts tab for details.</p>
            </div>
        </div>
    </div>
</div>
