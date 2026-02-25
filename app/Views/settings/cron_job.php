<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-clock text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Cron Job</h5>
        <small class="text-muted">Scheduled background tasks configuration</small>
    </div>
</div>

<div class="alert border-0 shadow-sm mb-4" style="border-radius:10px;background:#f0fdf4;">
    <i class="fa-solid fa-circle-info me-2 text-success"></i>
    <strong>Setup your cron job</strong> in your server's crontab to run this URL every minute:
</div>

<div class="mb-4">
    <label class="form-label">Cron Command</label>
    <div class="input-group">
        <span class="input-group-text bg-dark text-light" style="border-radius:8px 0 0 8px;font-size:.8rem;">$</span>
        <code class="form-control bg-dark text-success font-monospace py-2" id="cronCmd"
              style="border-radius:0 8px 8px 0;font-size:.82rem;user-select:all;">
            * * * * * curl -s <?= site_url('cron/run') ?> > /dev/null 2>&1
        </code>
        <button class="btn btn-outline-secondary" type="button"
                onclick="navigator.clipboard.writeText(document.getElementById('cronCmd').textContent.trim())">
            <i class="fa-solid fa-copy"></i>
        </button>
    </div>
    <div class="form-text">Or visit: <a href="<?= site_url('cron/run') ?>" target="_blank"><?= site_url('cron/run') ?></a></div>
</div>

<?= form_open('settings/save_cron_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="cron">

<div class="settings-section-hdr">Cron Secret Key</div>
<div class="row g-3 mb-1">
    <div class="col-md-8">
        <label for="cron_secret_key" class="form-label">Secret Key</label>
        <div class="input-group">
            <input type="text" name="cron_secret_key" id="cron_secret_key"
                   class="form-control font-monospace"
                   value="<?= esc(setting('cron_secret_key', bin2hex(random_bytes(16)))) ?>">
            <button class="btn btn-outline-secondary" type="button"
                    onclick="document.getElementById('cron_secret_key').value = [...Array(32)].map(()=>Math.random().toString(36)[2]).join('')">
                <i class="fa-solid fa-rotate"></i> Regenerate
            </button>
        </div>
        <div class="form-text">Append as <code>?key=YOUR_SECRET</code> to the cron URL for security.</div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save Cron Settings</button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;} .font-monospace{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace!important;}</style>
<?= $this->endSection() ?>
