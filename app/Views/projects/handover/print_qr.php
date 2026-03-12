<?php $this->extend('layouts/dashboard'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold mb-0">Asset QR Labels</h4>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print Labels
        </button>
    </div>

    <div class="row g-4 print-grid">
        <?php foreach ($assets as $asset): 
            $qrUrl = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode(site_url("field/asset/" . $asset['id']));
        ?>
        <div class="col-md-4 col-sm-6">
            <div class="card border p-3 text-center h-100 shadow-sm" style="border-width: 2px !important;">
                <div class="fw-bold text-uppercase small mb-2 border-bottom pb-1"><?= esc($project['title']) ?></div>
                <div class="h6 fw-bold mb-3"><?= esc($asset['asset_name']) ?></div>
                <img src="<?= $qrUrl ?>" alt="QR Code" class="img-fluid mb-3 mx-auto" style="max-width: 120px;">
                <div class="text-muted mb-1" style="font-size: 0.7rem;">Asset Tag: <strong><?= esc($asset['asset_tag']) ?></strong></div>
                <div class="text-muted" style="font-size: 0.6rem;">Scan for O&M / Warranty Info</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    @media print {
        .print-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .card {
            break-inside: avoid;
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
    }
</style>
<?php $this->endSection(); ?>
