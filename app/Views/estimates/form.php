<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= site_url('estimates') ?>" method="post">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="client_id">Client</label>
                    <select name="client_id" id="client_id" class="form-control" required>
                        <option value="">Select Client</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= esc($client['company_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="estimate_date">Estimate Date</label>
                    <input type="date" name="estimate_date" id="estimate_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-3 form-group">
                    <label for="valid_until">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                </div>
            </div>

            <hr>
            <h5>Items</h5>
            <div id="items-container">
                <div class="item-row row mb-2">
                    <div class="col-md-4">
                        <input type="text" name="items[0][title]" class="form-control" placeholder="Item Title" required>
                    </div>
                     <div class="col-md-4">
                        <input type="text" name="items[0][description]" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-1">
                         <input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][rate]" class="form-control rate" value="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <span class="total-price">0.00</span>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="add-item-btn">Add Item</button>

            <div class="row mt-4">
                <div class="col-md-12 form-group">
                    <label for="note">Note</label>
                    <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Estimate</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const index = container.children.length;
        const html = `
            <div class="item-row row mb-2">
                <div class="col-md-4">
                    <input type="text" name="items[${index}][title]" class="form-control" placeholder="Item Title" required>
                </div>
                 <div class="col-md-4">
                    <input type="text" name="items[${index}][description]" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-1">
                        <input type="number" name="items[${index}][quantity]" class="form-control quantity" value="1" min="1" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${index}][rate]" class="form-control rate" value="0.00" min="0" step="0.01">
                </div>
                <div class="col-md-1">
                    <span class="total-price">0.00</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    });

    // Basic calculation script (enhancement needed for robustness)
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('rate')) {
            const row = e.target.closest('.item-row');
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            row.querySelector('.total-price').textContent = (qty * rate).toFixed(2);
        }
    });
</script>
<?= $this->endSection() ?>
