<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Create New Subscription Package</h5>
                </div>
                <div class="card-body">
                    <?= form_open('subscriptions/store') ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Package Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Pro Plan, Enterprise">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief tagline for the plan"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Billing Interval</label>
                            <select name="billing_interval" class="form-select">
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch pt-3">
                                <input class="form-check-input" type="checkbox" name="is_per_user" id="isPerUser">
                                <label class="form-check-label fw-bold" for="isPerUser">Per User Pricing Model</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Features (One per line)</label>
                            <textarea name="features" class="form-control" rows="5" placeholder="Unlimited Projects&#10;Full Financial Control&#10;Daily Logs..."></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100">Create Package</button>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
