<aside class="app-sidebar border-end bg-white">
    <div class="p-3">
        <h6 class="text-uppercase text-muted mb-3">Navigation</h6>
        <div class="list-group list-group-flush">
            <a href="<?= site_url('/') ?>" class="list-group-item list-group-item-action"><?= esc(t('dashboard')) ?></a>
            <a href="<?= site_url('projects') ?>" class="list-group-item list-group-item-action"><?= esc(t('Projects')) ?></a>
            
            <h6 class="text-uppercase text-muted mt-4 mb-2 ps-3" style="font-size: 0.75rem;">Planning & Execution</h6>
            <a href="<?= site_url('areas') ?>" class="list-group-item list-group-item-action">Area Management</a>
            <a href="<?= site_url('schedules') ?>" class="list-group-item list-group-item-action">Schedules</a>
            <a href="<?= site_url('contracts') ?>" class="list-group-item list-group-item-action">Contracts</a>

            <h6 class="text-uppercase text-muted mt-4 mb-2 ps-3" style="font-size: 0.75rem;">Field & Quality</h6>
            <a href="<?= site_url('rfis') ?>" class="list-group-item list-group-item-action">RFIs</a>
            <a href="<?= site_url('submittals') ?>" class="list-group-item list-group-item-action">Submittals</a>
            <a href="<?= site_url('drawings') ?>" class="list-group-item list-group-item-action">Drawings</a>
            <a href="<?= site_url('inspections') ?>" class="list-group-item list-group-item-action">Inspections</a>
            
            <h6 class="text-uppercase text-muted mt-4 mb-2 ps-3" style="font-size: 0.75rem;">Resources & Finance</h6>
            <a href="<?= site_url('procurement') ?>" class="list-group-item list-group-item-action">Procurement</a>
            <a href="<?= site_url('inventory') ?>" class="list-group-item list-group-item-action">Inventory</a>
            <a href="<?= site_url('assets') ?>" class="list-group-item list-group-item-action">Assets</a>
            <a href="<?= site_url('payroll') ?>" class="list-group-item list-group-item-action">Payroll</a>
            <a href="<?= site_url('reports/financial/pnl') ?>" class="list-group-item list-group-item-action">P&L Report</a>

            <h6 class="text-uppercase text-muted mt-4 mb-2 ps-3" style="font-size: 0.75rem;">System</h6>
            <a href="<?= site_url('approval/requests') ?>" class="list-group-item list-group-item-action"><?= esc(t('approvals')) ?></a>
        </div>
    </div>
</aside>
