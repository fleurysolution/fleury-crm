<!DOCTYPE html>
<html>
<head>
    <title>Schedule Appointment</title>
    <link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/js/select2/select2.css">
    <link rel="stylesheet" type="text/css" href="assets/js/select2/select2-bootstrap.min.css">
</head>
<body>

<h2>Book an Appointment</h2>

<?php if (session()->getFlashdata('success')): ?>
    <p style="color: green;"><?= session()->getFlashdata('success') ?></p>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
<?php endif; ?>

<form method="post" action="<?= site_url('appointment/availableSlots') ?>">
    <?= csrf_field() ?>
    <label for="date">Choose Date:</label>
    <input type="date" name="date" required>
    <button type="submit">Show Available Slots</button>
</form>

</body>
</html>
