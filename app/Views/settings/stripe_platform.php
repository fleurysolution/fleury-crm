<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Platform Stripe Integration</h5>
                    <p class="text-muted small mb-0">These keys are used for the main BPMS247 subscription billing.</p>
                </div>
                <div class="card-body p-4">
                    <?= form_open('settings/save_platform_stripe') ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Environment Mode</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode_sandbox" value="sandbox" <?= ($mode === 'sandbox') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mode_sandbox">Sandbox (Test)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode_live" value="live" <?= ($mode === 'live') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mode_live">Live (Production)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-flask me-2"></i>Sandbox Credentials</h6>
                            <div class="mb-3">
                                <label class="form-label fw-600">Test Public Key</label>
                                <input type="text" name="test_pk" class="form-control" value="<?= esc($test_pk) ?>" placeholder="pk_test_...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Test Secret Key</label>
                                <input type="password" name="test_sk" class="form-control" value="<?= esc($test_sk) ?>" placeholder="sk_test_...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-rocket me-2"></i>Live Credentials</h6>
                            <div class="mb-3">
                                <label class="form-label fw-600">Live Public Key</label>
                                <input type="text" name="live_pk" class="form-control" value="<?= esc($live_pk) ?>" placeholder="pk_live_...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Live Secret Key</label>
                                <input type="password" name="live_sk" class="form-control" value="<?= esc($live_sk) ?>" placeholder="sk_live_...">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger"><i class="fa-solid fa-link me-2"></i>Webhook Signing Secret</label>
                        <p class="text-muted small">This secret is used to verify that events are sent by Stripe.</p>
                        <input type="text" name="webhook_secret" class="form-control" value="<?= esc($webhook_secret) ?>" placeholder="whsec_...">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Update Integration Settings</button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>

            <div class="alert alert-info mt-4 border-0 shadow-sm">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fa-solid fa-circle-info fa-2x text-info"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Webhook Configuration</h6>
                        <p class="mb-2">Ensure your Stripe dashboard is configured to send events to this endpoint:</p>
                        <code class="bg-light p-2 rounded d-block border mb-2"><?= site_url('webhooks/stripe') ?></code>
                        <small class="text-muted">Required events: <code>checkout.session.completed</code>, <code>customer.subscription.deleted</code>, <code>invoice.paid</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
