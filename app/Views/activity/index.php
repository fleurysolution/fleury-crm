<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php
// app/Views/activity/index.php — Global Activity Log (admin)
$actionColors = [
    'created' => 'success', 'updated' => 'primary', 'deleted' => 'danger',
    'approved' => 'info', 'rejected' => 'warning', 'submitted' => 'secondary',
    'paid' => 'success', 'marked_paid' => 'success',
];
$entityIcons = [
    'task'        => 'fa-list-check',
    'project'     => 'fa-folder-open',
    'rfi'         => 'fa-circle-question',
    'submittal'   => 'fa-file-arrow-up',
    'punch_list'  => 'fa-clipboard-check',
    'site_diary'  => 'fa-book-open',
    'contract'    => 'fa-file-contract',
    'boq'         => 'fa-table-list',
    'payment_cert'=> 'fa-file-invoice-dollar',
    'invoice'     => 'fa-receipt',
    'expense'     => 'fa-coins',
];
?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">
... [existing content truncated for brevity in replacement, but I will provide full section] ...
<script>
... [existing script] ...
</script>
<?= $this->endSection() ?>
