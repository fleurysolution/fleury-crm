<form action="<?= base_url('assets/return_asset') ?>" method="post">
    <label>Assignment:</label>
    <select name="assignment_id" required>
        <option value="">Select Assignment</option>
        <?php foreach($assignments as $a): ?>
            <option value="<?= $a->id ?>">
                <?= $a->asset_name ?> - <?= $a->model_name ?> (Assigned Qty: <?= $a->quantity ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label>Return Quantity:</label>
    <input type="number" name="quantity" min="1" required>

    <label>Status:</label>
    <select name="status">
        <option value="returned">Returned</option>
        <option value="damaged">Damaged</option>
        <option value="under_maintenance">Under Maintenance</option>
    </select>

    <label>Condition:</label>
    <select name="condition">
        <option value="good">Good</option>
        <option value="damaged">Damaged</option>
        <option value="needs_repair">Needs Repair</option>
    </select>

    <label>Remarks:</label>
    <textarea name="remarks"></textarea>

    <label>Return Date:</label>
    <input type="date" name="return_date" value="<?= date('Y-m-d') ?>" required>

    <button type="submit">Return Asset</button>
</form>
