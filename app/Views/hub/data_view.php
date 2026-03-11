<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><?= esc($object['label_plural']) ?></h4>
            <p class="text-muted small mb-0"><?= esc($object['description']) ?></p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addRecordModal">
            <i class="fa-solid fa-plus me-1"></i> New <?= esc($object['label_singular']) ?>
        </button>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <?php foreach ($fields as $field): ?>
                            <th><?= esc($field['field_label']) ?></th>
                            <?php endforeach; ?>
                            <th>Created At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="<?= count($fields) + 3 ?>" class="text-center py-5 text-muted">
                                No records found. Click "New <?= esc($object['label_singular']) ?>" to add one.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($records as $record): ?>
                        <tr>
                            <td class="ps-4 text-muted small">#<?= $record['id'] ?></td>
                            <?php foreach ($fields as $field): ?>
                            <td><?= esc($record['field_' . $field['id']] ?? '-') ?></td>
                            <?php endforeach; ?>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($record['created_at'])) ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border-0"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Record Modal -->
<div class="modal fade" id="addRecordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New <?= esc($object['label_singular']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('hub/' . $object['name'] . '/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <?php if (empty($fields)): ?>
                    <div class="alert alert-warning py-2 small">
                        No custom fields defined for this object. <a href="<?= site_url('settings/custom-fields') ?>">Define fields first</a>.
                    </div>
                    <?php endif; ?>
                    
                    <?php foreach ($fields as $field): ?>
                    <div class="mb-3">
                        <label class="form-label"><?= esc($field['field_label']) ?> <?= $field['is_required'] ? '<span class="text-danger">*</span>' : '' ?></label>
                        <?php if ($field['field_type'] === 'text'): ?>
                            <input type="text" name="field_<?= $field['id'] ?>" class="form-control" <?= $field['is_required'] ? 'required' : '' ?>>
                        <?php elseif ($field['field_type'] === 'number'): ?>
                            <input type="number" name="field_<?= $field['id'] ?>" class="form-control" <?= $field['is_required'] ? 'required' : '' ?>>
                        <?php elseif ($field['field_type'] === 'date'): ?>
                            <input type="date" name="field_<?= $field['id'] ?>" class="form-control" <?= $field['is_required'] ? 'required' : '' ?>>
                        <?php elseif ($field['field_type'] === 'select'): ?>
                            <select name="field_<?= $field['id'] ?>" class="form-select" <?= $field['is_required'] ? 'required' : '' ?>>
                                <option value="">Select option...</option>
                                <?php 
                                    $opts = explode(',', $field['options']);
                                    foreach ($opts as $opt): 
                                ?>
                                <option value="<?= trim($opt) ?>"><?= trim($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($field['field_type'] === 'lookup'): ?>
                            <select name="field_<?= $field['id'] ?>" class="form-select" <?= $field['is_required'] ? 'required' : '' ?>>
                                <option value="">Select <?= esc($field['options']) ?>...</option>
                                <?php if (isset($lookupOptions[$field['id']])): ?>
                                    <?php foreach ($lookupOptions[$field['id']] as $opt): ?>
                                        <option value="<?= $opt['id'] ?>"><?= esc($opt['label']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save <?= esc($object['label_singular']) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
