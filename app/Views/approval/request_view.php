<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1"><?= esc(t('approval_request_title')) ?> #<?= (int)($request['id'] ?? 0) ?></h4>
        <p class="text-muted mb-0"><?= esc(t('request_summary')) ?></p>
    </div>
    <a href="<?= site_url('approval/requests') ?>" class="btn btn-outline-secondary"><?= esc(t('back')) ?></a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('request_key')) ?></div><div class="col-8"><?= esc($request['request_key'] ?? '-') ?></div></div>
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('module')) ?></div><div class="col-8"><?= esc($request['module_key'] ?? '-') ?></div></div>
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('entity')) ?></div><div class="col-8"><?= esc(($request['entity_type'] ?? '-') . ' #' . ($request['entity_id'] ?? '-')) ?></div></div>
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('status')) ?></div><div class="col-8"><?= esc($request['status'] ?? '-') ?></div></div>
                <div class="row"><div class="col-4 text-muted"><?= esc(t('current_step')) ?></div><div class="col-8"><?= esc($request['current_step_no'] ?? '-') ?></div></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="mb-3"><?= esc(t('workflow_steps')) ?></h6>
                <?php if (!empty($request['steps'])): ?>
                    <div class="list-group">
                        <?php foreach ($request['steps'] as $step): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong>Step <?= (int)$step['step_no'] ?></strong>
                                    <span class="badge bg-light text-dark border"><?= esc($step['status']) ?></span>
                                </div>
                                <small class="text-muted">
                                    Acted by: <?= esc($step['acted_by'] ?? '-') ?> |
                                    At: <?= esc($step['acted_at'] ?? '-') ?>
                                </small>
                                <?php if (!empty($step['action_note'])): ?>
                                    <div class="mt-2"><em><?= esc($step['action_note']) ?></em></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No steps found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <?php if (($request['status'] ?? '') === 'pending'): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3"><?= esc(t('take_action')) ?></h6>

                    <form method="post" action="<?= site_url('approval/requests/' . (int)$request['id'] . '/approve') ?>" class="mb-3">
                        <?= csrf_field() ?>
                        <label class="form-label"><?= esc(t('approval_note')) ?></label>
                        <textarea name="note" class="form-control mb-2" rows="3"></textarea>
                        <button type="submit" class="btn btn-success w-100"><?= esc(t('approve')) ?></button>
                    </form>

                    <form method="post" action="<?= site_url('approval/requests/' . (int)$request['id'] . '/reject') ?>">
                        <?= csrf_field() ?>
                        <label class="form-label"><?= esc(t('rejection_reason')) ?></label>
                        <textarea name="note" class="form-control mb-2" rows="3" required></textarea>
                        <button type="submit" class="btn btn-danger w-100"><?= esc(t('reject')) ?></button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
