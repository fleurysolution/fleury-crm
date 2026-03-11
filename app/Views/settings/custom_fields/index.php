<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Custom Fields</h4>
            <p class="text-muted small mb-0">Manage dynamic fields for your objects and projects.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFieldModal">
            <i class="fa-solid fa-plus me-2"></i>Add Custom Field
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Label</th>
                            <th>Name</th>
                            <th>Object Type</th>
                            <th>Type</th>
                            <th>Required</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fields as $field): ?>
                        <tr>
                            <td class="ps-4 fw-semibold"><?= esc($field['field_label']) ?></td>
                            <td><code><?= esc($field['field_name']) ?></code></td>
                            <td><span class="badge bg-info"><?= esc(ucfirst($field['object_type'])) ?></span></td>
                            <td><?= esc(ucfirst($field['field_type'])) ?></td>
                            <td><?= $field['is_required'] ? '<span class="text-danger">Yes</span>' : 'No' ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteField(<?= $field['id'] ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; if (empty($fields)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No custom fields found. Click "Add Custom Field" to get started.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Field Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('settings/custom-fields/store') ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Object Type</label>
                    <select name="object_type" class="form-select" required>
                        <option value="projects">Projects</option>
                        <option value="clients">Clients</option>
                        <option value="leads">Leads</option>
                        <?php foreach ($custom_objects as $obj): ?>
                        <option value="<?= esc($obj['name']) ?>"><?= esc($obj['label_singular']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Field Label</label>
                    <input type="text" name="field_label" class="form-control" placeholder="e.g. Project Priority" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Field Name (System Name)</label>
                    <input type="text" name="field_name" class="form-control" placeholder="e.g. project_priority" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Field Type</label>
                    <select name="field_type" class="form-select" required>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="select">Dropdown (Select)</option>
                        <option value="lookup">Lookup (Relationship)</option>
                    </select>
                </div>
                <div class="mb-3" id="lookupTargetContainer" style="display:none;">
                    <label class="form-label small fw-bold">Target Object (to link to)</label>
                    <select name="lookup_target" class="form-select">
                        <option value="projects">Projects</option>
                        <option value="clients">Clients</option>
                        <option value="leads">Leads</option>
                        <?php foreach ($custom_objects as $obj): ?>
                        <option value="<?= esc($obj['name']) ?>"><?= esc($obj['label_singular']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3" id="optionsContainer">
                    <label class="form-label small fw-bold" id="optionsLabel">Options (for Dropdown, comma separated)</label>
                    <textarea name="options" class="form-control" rows="2" placeholder="Option 1, Option 2, Option 3"></textarea>
                </div>
                <div class="form-check pt-2">
                    <input class="form-check-input" type="checkbox" name="is_required" id="is_required">
                    <label class="form-check-label" for="is_required">Required Field</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Field</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    document.getElementsByName('field_type')[0].addEventListener('change', function() {
        const type = this.value;
        document.getElementById('lookupTargetContainer').style.display = (type === 'lookup') ? 'block' : 'none';
        document.getElementById('optionsContainer').style.display = (type === 'select') ? 'block' : 'none';
    });

    async function deleteField(id) {
        if (confirm('Are you sure you want to delete this custom field? Data stored in this field will be lost.')) {
            const r = await fetch('<?= site_url('settings/custom-fields/delete/') ?>' + id, { method: 'POST' });
            const d = await r.json();
            if (d.success) location.reload();
            else alert(d.message);
        }
    }
</script>
<?= $this->endSection() ?>
