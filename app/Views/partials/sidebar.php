<aside class="app-sidebar border-end bg-white">
    <div class="p-3">
        <h6 class="text-uppercase text-muted mb-3">Navigation</h6>
        <div class="list-group list-group-flush">
            <a href="<?= site_url('/') ?>" class="list-group-item list-group-item-action"><?= esc(t('dashboard')) ?></a>
            <a href="<?= site_url('approval/requests') ?>" class="list-group-item list-group-item-action"><?= esc(t('approvals')) ?></a>
        </div>
    </div>
</aside>
