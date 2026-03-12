<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Estimate Builder: <?= esc($estimate['title']) ?>
                </h2>
                <div class="text-muted mt-1">
                    Project: <strong><?= esc($project['title']) ?></strong> | Status: <span class="badge bg-blue-lt"><?= esc($estimate['status']) ?></span>
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="<?= site_url("projects/{$project['id']}?tab=estimates") ?>" class="btn">
                        <i class="ti ti-arrow-left me-1"></i> Back to Project
                    </a>
                    <button onclick="window.print()" class="btn btn-primary d-none d-sm-inline-block">
                        <i class="ti ti-printer me-1"></i> Print / Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Sidebar Recap -->
            <div class="col-md-3">
                <div class="card bg-primary text-primary-content shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h3 class="card-title text-white">Budget Recap</h3>
                        <div class="mb-3">
                            <div class="text-white-50 small uppercase">Direct Costs (Subtotal)</div>
                            <div class="h3 mb-0 text-white"><?= number_to_currency($estimate['total_amount'], 'USD') ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-white-50 small uppercase">General Conditions (GCs)</div>
                            <div class="h3 mb-0 text-white"><?= number_to_currency($gcTotal, 'USD') ?></div>
                        </div>
                        <hr class="my-3 opacity-20">
                        <div>
                            <div class="text-white-50 small uppercase fw-bold">Grand Total Budget</div>
                            <div class="h1 mb-0 text-white"><?= number_to_currency($grandTotal, 'USD') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tab-direct" class="nav-link active" data-bs-toggle="tab">
                                    <i class="ti ti-list-check me-1"></i> 1. Direct Costs (BOQ)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-gcs" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-building-skyscraper me-1"></i> 2. General Conditions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-notes" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-notebook me-1"></i> 3. Clarifications & Risks
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Direct Costs Tab -->
                            <div class="tab-pane active show" id="tab-direct">
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="m-0">Estimate Line Items</h4>
                                    <button class="btn btn-sm btn-outline-primary ms-auto" data-bs-toggle="modal" data-bs-target="#modal-item">
                                        <i class="ti ti-plus me-1"></i> Add Item
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cost Code</th>
                                                <th>Description</th>
                                                <th class="text-end">Qty</th>
                                                <th class="text-end">Unit Cost</th>
                                                <th class="text-end">Total</th>
                                                <th class="w-1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><span class="text-muted"><?= esc($item['cost_code']) ?></span></td>
                                                <td class="fw-bold"><?= esc($item['description']) ?></td>
                                                <td class="text-end"><?= number_format($item['quantity'], 2) ?> <?= esc($item['unit']) ?></td>
                                                <td class="text-end"><?= number_to_currency($item['unit_cost'], 'USD') ?></td>
                                                <td class="text-end fw-bold"><?= number_to_currency($item['total_cost'], 'USD') ?></td>
                                                <td>
                                                    <a href="<?= site_url("estimates/{$estimate['id']}/items/{$item['id']}/delete") ?>" class="text-danger" onclick="return confirm('Delete item?')">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if(empty($items)): ?>
                                            <tr><td colspan="6" class="text-center py-4 text-muted small">No direct cost items added yet.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- GCs Tab -->
                            <div class="tab-pane" id="tab-gcs">
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="m-0">General Conditions (Staff, IT, Overhead)</h4>
                                    <button class="btn btn-sm btn-outline-primary ms-auto" data-bs-toggle="modal" data-bs-target="#modal-gc">
                                        <i class="ti ti-plus me-1"></i> Add GC Item
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Description</th>
                                                <th class="text-end">Amount</th>
                                                <th class="w-1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($gcs as $gc): ?>
                                            <tr>
                                                <td><span class="badge bg-azure-lt"><?= esc($gc['category']) ?></span></td>
                                                <td><?= esc($gc['description']) ?></td>
                                                <td class="text-end fw-bold"><?= number_to_currency($gc['amount'], 'USD') ?></td>
                                                <td>
                                                    <button class="btn btn-link link-danger p-0" onclick="deleteGC(<?= $gc['id'] ?>)">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if(empty($gcs)): ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted small">No general conditions added yet.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div class="tab-pane" id="tab-notes">
                                <form id="form-metadata">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Project Clarifications & Assumptions</label>
                                        <textarea name="clarifications" class="form-control" rows="8" placeholder="Enter boundaries of the estimate, scope exclusions, etc..."><?= esc($estimate['clarifications'] ?? '') ?></textarea>
                                        <div class="form-hint small mt-1 italic">Note any specific conditions that justify the cost logic.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-danger">Estimate Risk Summary</label>
                                        <textarea name="risk_summary" class="form-control" rows="6" placeholder="Document risks that might affect budget or timeline..."><?= esc($estimate['risk_summary'] ?? '') ?></textarea>
                                        <div class="form-hint small mt-1 italic">Identify potential cost escalators or supply chain risks.</div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="ti ti-device-floppy me-1"></i> Save Metadata
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal modal-blur fade" id="modal-item" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("estimates/{$estimate['id']}/items") ?>" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Direct Cost Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cost Code</label>
                    <input type="text" name="cost_code" class="form-control" placeholder="e.g. 03-3000">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-4">
                        <label class="form-label">Qty</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" value="1" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control" placeholder="EA">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Unit Cost</label>
                        <input type="number" step="0.01" name="unit_cost" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add to Estimate</button>
            </div>
        </form>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-gc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add General Condition Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select id="gc-category" class="form-select">
                        <option value="Staff Salaries">Staff Salaries</option>
                        <option value="IT & Software">IT & Software</option>
                        <option value="Site Logistics">Site Logistics</option>
                        <option value="Travel & Lodging">Travel & Lodging</option>
                        <option value="Overhead / Fee">Overhead / Fee</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" id="gc-description" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estimated Amount (Total)</label>
                    <input type="number" step="0.01" id="gc-amount" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="submitGC()">Save GC Item</button>
            </div>
        </div>
    </div>
</div>

<script>
async function submitGC() {
    const data = new FormData();
    data.append('category', document.getElementById('gc-category').value);
    data.append('description', document.getElementById('gc-description').value);
    data.append('amount', document.getElementById('gc-amount').value);

    const res = await fetch('<?= site_url("estimates/{$estimate['id']}/gcs") ?>', {
        method: 'POST',
        body: data
    });
    
    if (res.ok) window.location.reload();
}

async function deleteGC(gcId) {
    if (!confirm('Delete this GC item?')) return;
    const res = await fetch(`<?= site_url("estimates/{$estimate['id']}/gcs") ?>/${gcId}/delete`, {
        method: 'POST'
    });
    if (res.ok) window.location.reload();
}

document.getElementById('form-metadata').onsubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(e.target);
    const res = await fetch('<?= site_url("estimates/{$estimate['id']}/metadata") ?>', {
        method: 'POST',
        body: data
    });
    if (res.ok) {
        Swal.fire({
            title: 'Success!',
            text: 'Clarifications and Risks saved successfully.',
            icon: 'success',
            confirmButtonText: 'Great'
        });
    }
};
</script>

<?= $this->endSection() ?>
