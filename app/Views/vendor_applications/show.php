<?= $this->extend('layouts/dashboard'); ?>

<?= $this->section('content'); ?>
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="page-title">Review Vendor Application</h4>
        <a href="<?= site_url('vendor-applications') ?>" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Company Profile</h5>
                
                <div class="row mb-3 mt-4">
                    <div class="col-sm-4 text-muted">Company Name</div>
                    <div class="col-sm-8 fw-semibold"><?= esc($application['company_name']) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Primary Contact</div>
                    <div class="col-sm-8"><?= esc($application['contact_name']) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Email Address</div>
                    <div class="col-sm-8"><a href="mailto:<?= esc($application['email']) ?>"><?= esc($application['email']) ?></a></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Phone Number</div>
                    <div class="col-sm-8"><?= esc($application['phone'] ?: 'N/A') ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Trade Type</div>
                    <div class="col-sm-8"><?= esc($application['trade_type'] ?: 'N/A') ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Tax ID / EIN</div>
                    <div class="col-sm-8"><?= esc($application['tax_id'] ?: 'N/A') ?></div>
                </div>

                <hr class="my-4">
                
                <h5 class="card-title mb-3">Compliance Documents</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="border p-3 rounded">
                            <h6>W9 Form</h6>
                            <?php if ($application['w9_path']): ?>
                                <a href="<?= base_url(esc($application['w9_path'])) ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-file-pdf"></i> View Attachment</a>
                            <?php else: ?>
                                <span class="text-muted">No document provided</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="border p-3 rounded">
                            <h6>Certificate of Insurance</h6>
                            <?php if ($application['insurance_path']): ?>
                                <a href="<?= base_url(esc($application['insurance_path'])) ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-file-pdf"></i> View Attachment</a>
                            <?php else: ?>
                                <span class="text-muted">No document provided</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title mb-4">Application Status</h4>
                
                <?php if ($application['status'] === 'pending'): ?>
                    <div class="alert alert-warning">
                        <strong>Pending Review</strong><br>
                        This application requires your approval.
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-success btn-lg" onclick="updateStatus('approve')">
                            <i class="fa-solid fa-check me-1"></i> Approve & Create Account
                        </button>
                        <button class="btn btn-outline-danger" onclick="updateStatus('reject')">
                            <i class="fa-solid fa-xmark me-1"></i> Reject
                        </button>
                    </div>
                <?php elseif ($application['status'] === 'approved'): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check fs-1 mb-2"></i><br>
                        <strong>Approved</strong><br>
                        User account has been provisioned.
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-xmark fs-1 mb-2"></i><br>
                        <strong>Rejected</strong>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(action) {
    if (!confirm('Are you sure you want to ' + action + ' this vendor application?')) return;
    
    $.post('<?= site_url('vendor-applications/' . $application['id']) ?>/' + action, {
        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
    }, function(res) {
        if(res.success) {
            alert(res.message);
            location.reload();
        } else {
            alert(res.message);
        }
    }).fail(function() {
        alert("An error occurred. Please try again.");
    });
}
</script>
<?= $this->endSection(); ?>
