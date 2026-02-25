<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Regions</h4>

<form id="region-form" method="post">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            <label>Region <span class="text-danger">*</span></label>
            <input type="text" name="name" required class="form-control">
            <input type="hidden" name="id">
        </div>

        <div class="form-group col-md-6 mb-3">
            <label>Region Code</label>
            <input type="text" name="region_code" class="form-control">
        </div>

        <div class="form-group col-md-8 mb-3">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control"></textarea>
        </div>

        <div class="form-group col-md-4 mb-3 d-flex align-items-end">
            <input type="submit" class="btn btn-success" value="Save">
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Manage Regions</h5>
        <div class="table-responsive">
            <table id="region-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Region</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($regions)): ?>
                        <?php foreach ($regions as $r): ?>
                            <tr data-id="<?= $r['id'] ?>">
                                <td><?= $r['id'] ?></td>
                                <td><?= esc($r['name']) ?></td>
                                <td><?= esc($r['code'] ?? '') ?></td>
                                <td><?= esc($r['description'] ?? '') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editRegion(<?= $r['id'] ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteRegion(<?= $r['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No regions found. Add one above.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#region-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= site_url('settings/branches/regions/save'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (!res.success) { alert(res.message || 'Failed'); return; }
                alert(res.message || 'Saved');
                location.reload();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Server error while saving region.');
            }
        });
    });
});

function deleteRegion(id) {
    if (!confirm('Delete this region?')) return;

    $.ajax({
        url: '<?= site_url('settings/branches/regions/delete/'); ?>' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            alert(res.message || 'Deleted');
            $('tr[data-id="'+id+'"]').remove();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Server error while deleting region.');
        }
    });
}

function editRegion(id) {
    $.getJSON('<?= site_url('settings/branches/regions/get/'); ?>' + id, function(data) {
        if (!data) return;
        $('input[name=id]').val(data.id);
        $('input[name=name]').val(data.name);
        $('input[name=region_code]').val(data.code);
        $('textarea[name=description]').val(data.description);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
}
</script>

<?= $this->endSection() ?>
