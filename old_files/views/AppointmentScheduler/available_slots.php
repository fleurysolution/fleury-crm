<!DOCTYPE html>
<html>
<head>
    <title>Available Slots for <?= $date ?></title>
</head>
<body>

<h2>Available Time Slots – <?= $date ?></h2>

<form method="post" action="<?= site_url('appointment/book') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="date" value="<?= $date ?>">

    <label>Name:</label>
    <input type="text" name="name" required><br><br>

    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Select a Time Slot:</label><br>
    <?php foreach ($slots as $slot): ?>
        <?php if ($slot['available']): ?>
            <label>
                <input type="radio" name="time" value="<?= $slot['time'] ?>" required>
                <?= date('h:i A', strtotime($slot['time'])) ?>
            </label><br>
        <?php else: ?>
            <label style="color: grey;">
                <?= date('h:i A', strtotime($slot['time'])) ?> – Booked
            </label><br>
        <?php endif; ?>
    <?php endforeach; ?>

    <br>
    <button type="submit">Book Appointment</button>
</form>

</body>
</html>
