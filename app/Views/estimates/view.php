<?= $this->extend('layouts/dashboard') ?>

<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">
    
    <div class="content-header px-4 pt-4 pb-0 d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">ESTIMATE #<?= esc($estimate['id']) ?></h1>
            <p class="text-muted small mb-0 mt-1">
                <span class="badge bg-<?= $estimate['status'] === 'accepted' ? 'success' : ($estimate['status'] === 'sent' ? 'info' : 'secondary') ?>-subtle text-<?= $estimate['status'] === 'accepted' ? 'success' : ($estimate['status'] === 'sent' ? 'info' : 'secondary') ?> rounded-pill px-3 py-1 me-2">
                    <?= esc(ucfirst($estimate['status'])) ?>
                </span>
                Created: <?= date('Y-m-d', strtotime($estimate['estimate_date'])) ?>
            </p>
        </div>
    </div>

    <div class="content px-4">
        <div class="row">
            <!-- Left Sidebar Toolbar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="<?= site_url('estimates/' . $estimate['id'] . '/pdf') ?>" class="list-group-item list-group-item-action py-3 border-0">
                                <i class="fa-solid fa-download fa-fw text-secondary me-2"></i> Download PDF
                            </a>
                            <a href="<?= site_url('estimates/' . $estimate['id'] . '/pdf?view=true') ?>" target="_blank" class="list-group-item list-group-item-action py-3 border-0">
                                <i class="fa-solid fa-file-pdf fa-fw text-secondary me-2"></i> View PDF
                            </a>
                            <button onclick="window.print()" class="list-group-item list-group-item-action py-3 border-0">
                                <i class="fa-solid fa-print fa-fw text-secondary me-2"></i> Print estimate
                            </button>
                            <a href="<?= site_url('estimates/edit/' . $estimate['id']) ?>" class="list-group-item list-group-item-action py-3 border-0">
                                <i class="fa-solid fa-pen-to-square fa-fw text-secondary me-2"></i> Edit estimate
                            </a>
                            <a href="<?= site_url('estimates/' . $estimate['id'] . '/clone') ?>" class="list-group-item list-group-item-action py-3 border-0">
                                <i class="fa-solid fa-copy fa-fw text-secondary me-2"></i> Clone Estimate
                            </a>
                            
                            <div class="border-top my-1"></div>
                            
                            <form action="<?= site_url('estimates/' . $estimate['id'] . '/status') ?>" method="post" class="m-0 border-0 p-0">
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" class="list-group-item list-group-item-action py-3 border-0 text-success fw-bold">
                                    <i class="fa-solid fa-check fa-fw me-2"></i> Mark as Accepted
                                </button>
                            </form>
                            <form action="<?= site_url('estimates/' . $estimate['id'] . '/status') ?>" method="post" class="m-0 border-0 p-0">
                                <input type="hidden" name="status" value="declined">
                                <button type="submit" class="list-group-item list-group-item-action py-3 border-0 text-danger fw-bold">
                                    <i class="fa-solid fa-xmark fa-fw me-2"></i> Mark as Declined
                                </button>
                            </form>
                            <a href="<?= site_url('estimates/' . $estimate['id'] . '/send') ?>" class="list-group-item list-group-item-action py-3 border-0 text-primary fw-bold">
                                <i class="fa-solid fa-envelope fa-fw me-2"></i> Send to client
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-dark">Estimate Info</h6>
                        <dl class="row mb-0 small text-muted">
                            <dt class="col-sm-5 fw-medium">Client:</dt>
                            <dd class="col-sm-7"><a href="<?= site_url('clients/' . $estimate['client_id']) ?>"><?= esc($client['company_name'] ?? 'Unknown') ?></a></dd>
                            <dt class="col-sm-5 fw-medium">Status:</dt>
                            <dd class="col-sm-7"><?= esc(ucfirst($estimate['status'])) ?></dd>
                            <dt class="col-sm-5 fw-medium">Valid Until:</dt>
                            <dd class="col-sm-7"><?= date('Y-m-d', strtotime($estimate['valid_until'])) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Main Canvas -->
            <div class="col-lg-9">
                <!-- Preview Canvas -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-4 p-md-5 bg-white">
                        
                        <!-- Header -->
                        <div class="row border-bottom pb-4 mb-4">
                            <div class="col-sm-6">
                                <h2 class="fw-black mb-2" style="letter-spacing: -1px;">ESTIMATE</h2>
                                <p class="text-muted mb-0">#<?= esc($estimate['id']) ?></p>
                            </div>
                            <div class="col-sm-6 text-md-end">
                                <!-- Company details - usually pulled from settings, hardcoded per request -->
                                <h5 class="fw-bold mb-1 text-dark">Fleury Solution LLC</h5>
                                <p class="text-muted small mb-0">
                                    1540 Hwy 138 SE, Suite 3K Conyers, GA 30013<br>
                                    Phone: +1 770 410 8378<br>
                                    Email: admin@fleurysolutions.com<br>
                                    Website: https://fleurysolutions.com
                                </p>
                            </div>
                        </div>

                        <!-- To Block -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h6 class="text-secondary text-uppercase fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Estimate To</h6>
                                <?php if ($client): ?>
                                    <h5 class="fw-bold text-dark mb-1"><?= esc($client['company_name']) ?></h5>
                                    <p class="text-muted small mb-0">
                                        <?= esc($client['address']) ?><br>
                                        <?= esc($client['city']) ?>, <?= esc($client['state']) ?> <?= esc($client['zip']) ?><br>
                                        <?= esc($client['country']) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-danger small">Client missing or deleted.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Line Items -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-light text-secondary text-uppercase" style="font-size: 0.8rem;">
                                    <tr>
                                        <th class="py-3">Item</th>
                                        <th class="py-3 text-end" style="width: 100px;">Qty</th>
                                        <th class="py-3 text-end" style="width: 150px;">Rate</th>
                                        <th class="py-3 text-end" style="width: 150px;">Total</th>
                                        <th class="py-3 text-center" style="width: 60px;"><i class="fa-solid fa-cog"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = 0; ?>
                                    <?php if (empty($items)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No record found. Use the Item Manager below to add line items.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($items as $item): 
                                            $itemTotal = $item['quantity'] * $item['rate']; 
                                            $subtotal += $itemTotal; 
                                        ?>
                                            <tr>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark"><?= esc($item['title']) ?></div>
                                                    <div class="text-muted small mt-1"><?= nl2br(esc($item['description'])) ?></div>
                                                </td>
                                                <td class="py-3 text-end align-middle"><?= $item['quantity'] ?></td>
                                                <td class="py-3 text-end align-middle"><?= number_format($item['rate'], 2) ?></td>
                                                <td class="py-3 text-end fw-bold text-dark align-middle"><?= number_format($itemTotal, 2) ?></td>
                                                <td class="py-3 text-center align-middle">
                                                    <form action="<?= site_url('estimates/' . $estimate['id'] . '/items/' . $item['id'] . '/delete') ?>" method="post" class="m-0">
                                                        <button type="submit" class="btn btn-sm text-danger p-0" title="Delete Item" onclick="return confirm('Delete this line item?');"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-muted">Sub Total</td>
                                        <td class="text-end fw-bold py-3 text-dark"><?= number_format($subtotal, 2) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-end fw-black py-3" style="font-size:1.1rem;">TOTAL</td>
                                        <td class="text-end fw-black text-primary py-3" style="font-size:1.1rem;"><?= esc($estimate['currency_symbol']) ?><?= number_format($subtotal, 2) ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- Item Manager Form -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border-left: 4px solid #0d6efd !important;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i> Item Manager</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('estimates/' . $estimate['id'] . '/items') ?>" method="post">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <input type="text" class="form-control bg-light border-0" name="title" placeholder="Item Name / Title" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control bg-light border-0" name="quantity" placeholder="Qty" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control bg-light border-0" name="rate" placeholder="Rate ($)" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-plus me-1"></i> Add</button>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control bg-light border-0" name="description" rows="2" placeholder="Item Description (Optional)"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
