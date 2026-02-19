<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Cron Job</h4>

<div class="card border-info mb-3">
    <div class="card-body">
        <p class="card-text">
            Add this cron job to your cPanel or server crontab to run every minute:
        </p>
        <pre class="bg-light p-3 border rounded"><code>* * * * * php <?= FCPATH ?>index.php cron</code></pre>
        <p class="card-text small text-muted">
            Last Cron Job Run: <?= setting('last_cron_job_time') ? format_to_datetime(setting('last_cron_job_time')) : 'Never' ?>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
