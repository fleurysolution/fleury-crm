<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Client Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Company Name</div>
                    <div class="col-md-8"><?= esc($client['company_name']) ?></div>
                </div>
                 <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">Address</div>
                    <div class="col-md-8">
                        <?= esc($client['address']) ?><br>
                        <?= esc($client['city']) ?>, <?= esc($client['state']) ?> <?= esc($client['zip']) ?><br>
                        <?= esc($client['country']) ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">VAT Number</div>
                    <div class="col-md-8"><?= esc($client['vat_number'] ?: 'N/A') ?></div>
                </div>
                 <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-muted">GST Number</div>
                    <div class="col-md-8"><?= esc($client['gst_number'] ?: 'N/A') ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Latest Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted text-center py-4">No recent activity.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
             <div class="card-header">
                <h5 class="card-title mb-0">Contacts</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm">Add Contact</button>
                    <!-- List contacts here -->
                </div>
            </div>
        </div>
    </div>
</div>
