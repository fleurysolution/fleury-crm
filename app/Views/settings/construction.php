<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php
// Helper to get a setting value
$get = function(string $key, string $default = '') use ($settings): string {
    foreach ($settings as $s) {
        if ($s['key'] === $key) return $s['value'] ?? $default;
    }
    return $default;
};
?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-building-columns me-2 text-primary"></i>Construction Settings</h1>
    <p class="text-muted small mb-0 mt-1">Default values for your construction projects</p>
</div>

<div class="content px-4 pt-3 pb-4">
<div id="settingsMsg" class="alert d-none mb-3"></div>

<form id="constructionSettingsForm">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
<div class="row g-4">

    <!-- Company Info -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-semibold"><i class="fa-solid fa-building me-2 text-primary"></i>Company Information</h6>
            </div>
            <div class="card-body pt-2">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?= esc($get('company_name')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Company Email</label>
                    <input type="email" name="company_email" class="form-control" value="<?= esc($get('company_email')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="company_phone" class="form-control" value="<?= esc($get('company_phone')) ?>">
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Address</label>
                    <textarea name="company_address" class="form-control" rows="2"><?= esc($get('company_address')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Defaults -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-semibold"><i class="fa-solid fa-folder-open me-2 text-success"></i>Project Defaults</h6>
            </div>
            <div class="card-body pt-2">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Default Currency</label>
                        <select name="default_currency" class="form-select">
                            <?php foreach (['USD'=>'USD — US Dollar','EUR'=>'EUR — Euro','GBP'=>'GBP — British Pound','AED'=>'AED — UAE Dirham','SAR'=>'SAR — Saudi Riyal','INR'=>'INR — Indian Rupee','PKR'=>'PKR — Pakistani Rupee'] as $code=>$label): ?>
                            <option value="<?= $code ?>" <?= $get('default_currency')===$code?'selected':'' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Currency Symbol</label>
                        <input type="text" name="default_currency_symbol" class="form-control" value="<?= esc($get('default_currency_symbol','$')) ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Default Retention %</label>
                        <div class="input-group">
                            <input type="number" name="default_retention_pct" class="form-control" value="<?= esc($get('default_retention_pct','10')) ?>" min="0" max="100" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Gantt Working Days/Week</label>
                        <select name="gantt_working_days" class="form-select">
                            <?php foreach ([5=>'5 days (Mon–Fri)',6=>'6 days (Mon–Sat)',7=>'7 days'] as $d=>$l): ?>
                            <option value="<?= $d ?>" <?= $get('gantt_working_days','5')==$d?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Default Task Status</label>
                        <select name="task_default_status" class="form-select">
                            <?php foreach (['todo'=>'To Do','in_progress'=>'In Progress'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $get('task_default_status')===$v?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOQ Settings -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-semibold"><i class="fa-solid fa-table-list me-2 text-warning"></i>Bill of Quantities (BOQ)</h6>
            </div>
            <div class="card-body pt-2">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Unit List <span class="text-muted">(comma-separated)</span></label>
                    <input type="text" name="boq_unit_list" class="form-control" value="<?= esc($get('boq_unit_list','m2,m3,m,no,kg,tonnes,ls,hr')) ?>">
                    <div class="form-text">These appear as dropdown options in BOQ item rows.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Section Numbering Start</label>
                    <input type="number" name="boq_section_prefix" class="form-control" value="<?= esc($get('boq_section_prefix','1')) ?>" min="1">
                </div>
            </div>
        </div>
    </div>

    <!-- Finance Prefixes -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-semibold"><i class="fa-solid fa-coins me-2 text-info"></i>Finance Prefixes</h6>
            </div>
            <div class="card-body pt-2">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">IPC / Payment Certificate Prefix</label>
                    <input type="text" name="ipc_prefix" class="form-control" value="<?= esc($get('ipc_prefix','IPC-')) ?>">
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Contract Number Prefix</label>
                    <input type="text" name="contract_number_prefix" class="form-control" value="<?= esc($get('contract_number_prefix','CON-')) ?>">
                </div>
            </div>
        </div>
    </div>

</div><!-- .row -->

<div class="mt-4 d-flex gap-2 align-items-center">
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk me-1"></i>Save Settings
    </button>
    <span id="inlineMsg" class="small"></span>
</div>
</form>
</div>
</div>

<script>
document.getElementById('constructionSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    const r = await fetch('<?= site_url('settings/construction/save') ?>', {
        method:'POST', body:new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const d = await r.json();
    btn.disabled = false;

    const el = document.getElementById('inlineMsg');
    el.className = 'small text-' + (d.success ? 'success' : 'danger');
    el.textContent = d.message || (d.success ? 'Saved.' : 'Error.');
});
</script>

<?= $this->endSection() ?>
