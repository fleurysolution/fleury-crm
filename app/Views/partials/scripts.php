<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/datatables/datatables.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/toastr/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<?php if (!empty($pageScript)): ?>
<script src="<?= base_url('assets/js/' . $pageScript) ?>"></script>
<?php endif; ?>

<script>
<?php if (session()->getFlashdata('status')): ?>
toastr.success("<?= esc(session()->getFlashdata('status')) ?>");
<?php endif; ?>

<?php $errors = session()->getFlashdata('errors'); ?>
<?php if (!empty($errors) && is_array($errors)): ?>
toastr.error("<?= esc(implode(' | ', $errors)) ?>");
<?php endif; ?>
</script>
