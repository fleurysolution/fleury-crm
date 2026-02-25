<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Divisions</h4>

<form id="division-form" method="post">
    <div class="row">
        <div class="form-group col-md-4 mb-3">
            <label>Region <span class="text-danger">*</span></label>
            <select class="form-select" name="region_id" id="region" required>
                <option value="">Select Region</option>
                <?php if (!empty($regions)): ?>
                    <?php foreach ($regions as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label>Branch/Office <span class="text-danger">*</span></label>
            <select class="form-select" name="office_id" id="office" required>
                <option value="">Select Office</option>
            </select>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label>Division <span class="text-danger">*</span></label>
            <input type="text" name="name" required class="form-control">
            <input type="hidden" name="id">
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
        <h5 class="card-title mb-3">Manage Divisions</h5>
        <div class="table-responsive">
            <table id="division-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Division</th>
                        <th>Region</th>
                        <th>Office</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($divisions)): ?>
                        <?php foreach ($divisions as $d): ?>
                            <tr data-id="<?= $d['id'] ?>">
                                <td><?= $d['id'] ?></td>
                                <td><?= esc($d['name']) ?></td>
                                <td><?= esc($d['region_name'] ?? '-') ?></td>
                                <td><?= esc($d['office_name'] ?? '-') ?></td>
                                <td><?= esc($d['description'] ?? '') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editDivision(<?= $d['id'] ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteDivision(<?= $d['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No divisions found. Add one above.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#region').on('change', function() {
        const regionId = $(this).val();
        $('#office').html('<option value="">Select Office</option>');
        if (!regionId) return;

        $.getJSON('<?= site_url('settings/branches/offices/by-region/'); ?>' + regionId, function(offices) {
            let html = '<option value="">Select Office</option>';
            $.each(offices, function(_, o) {
                html += `<option value="${o.id}">${o.name}</option>`;
            });
            $('#office').html(html);
        });
    });

    $('#division-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= site_url('settings/branches/divisions/save'); ?>',
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
                alert('Server error while saving division.');
            }
        });
    });
});

function deleteDivision(id) {
    if (!confirm('Delete this division?')) return;

    $.ajax({
        url: '<?= site_url('settings/branches/divisions/delete/'); ?>' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            alert(res.message || 'Deleted');
            $('tr[data-id="'+id+'"]').remove();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Server error while deleting division.');
        }
    });
}

function editDivision(id) {
    $.getJSON('<?= site_url('settings/branches/divisions/get/'); ?>' + id, function(data) {
        if (!data) return;

        $('input[name=id]').val(data.id);
        $('#region').val(data.region_id).trigger('change');

        // wait for office dropdown to load via ajax
        setTimeout(function() {
            $('#office').val(data.office_id);
        }, 250);

        $('input[name=name]').val(data.name);
        $('textarea[name=description]').val(data.description);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
}
</script>

<?= $this->endSection() ?>
