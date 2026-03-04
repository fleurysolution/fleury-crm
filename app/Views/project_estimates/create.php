<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">
    <!-- Page Header -->
    <div class="content-header px-4 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h4 fw-bold mb-0">Create Estimate</h1>
            <p class="text-muted small mb-0 mt-1">Start a new estimate and assign it to a project</p>
        </div>
        <a href="<?= site_url('estimates') ?>" class="btn btn-light border text-secondary shadow-sm" style="border-radius:8px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Estimates
        </a>
    </div>

    <!-- Main Content -->
    <div class="content px-4 pt-4 pb-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-4 p-md-5">
                        <form action="<?= site_url('estimates') ?>" method="post">
                            <div class="mb-4">
                                <label for="project_id" class="form-label fw-semibold text-dark">Select Project <span class="text-danger">*</span></label>
                                <select class="form-select border-0 bg-light" id="project_id" name="project_id" required style="padding: 0.75rem 1rem;">
                                    <option value="" disabled selected>— Choose a Project —</option>
                                    <?php foreach ($projects as $proj): ?>
                                        <option value="<?= esc($proj['id']) ?>"><?= esc($proj['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small mt-2"><i class="fa-solid fa-circle-info me-1"></i>Estimates must be tied to a specific project.</div>
                            </div>

                            <div class="mb-5">
                                <label for="title" class="form-label fw-semibold text-dark">Estimate Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-0 bg-light" id="title" name="title" placeholder="e.g. Master Bedroom Addition, HVAC Overhaul, Initial Bid..." required style="padding: 0.75rem 1rem;">
                            </div>

                            <div class="d-flex justify-content-end border-top pt-4">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                                    Continue to Builder <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
