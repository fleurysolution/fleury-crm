<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Offices / Branches</h4>

<form id="office-form" method="post">
    <div class="row">
        <div class="form-group col-md-4 mb-3">
            <label>Region <span class="text-danger">*</span></label>
            <select class="form-select" name="region_id" required>
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
            <input type="text" name="name" required class="form-control">
            <input type="hidden" name="id">
        </div>

        <div class="form-group col-md-4 mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control">
        </div>

        <div class="form-group col-md-4 mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="form-group col-md-4 mb-3">
            <label>Phone</label>
            <input type="tel" name="phone" class="form-control">
        </div>

        <div class="form-group col-md-4 mb-3 d-flex align-items-end">
            <input type="submit" class="btn btn-success" value="Save">
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Manage Offices</h5>
        <div class="table-responsive">
            <table id="branch-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Office</th>
                        <th>Region</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($offices)): ?>
                        <?php foreach ($offices as $o): ?>
                            <tr data-id="<?= $o['id'] ?>">
                                <td><?= $o['id'] ?></td>
                                <td><?= esc($o['name']) ?></td>
                                <td><?= esc($o['region_name'] ?? '-') ?></td>
                                <td><?= esc($o['address'] ?? '') ?></td>
                                <td><?= esc($o['phone'] ?? '') ?></td>
                                <td><?= esc($o['email'] ?? '') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editOffice(<?= $o['id'] ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteOffice(<?= $o['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No offices found. Add one above.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#office-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= site_url('settings/branches/offices/save'); ?>',
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
                alert('Server error while saving office.');
            }
        });
    });
});

function deleteOffice(id) {
    if (!confirm('Delete this office?')) return;

    $.ajax({
        url: '<?= site_url('settings/branches/offices/delete/'); ?>' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            alert(res.message || 'Deleted');
            $('tr[data-id="'+id+'"]').remove();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Server error while deleting office.');
        }
    });
}

function editOffice(id) {
    $.getJSON('<?= site_url('settings/branches/offices/get/'); ?>' + id, function(data) {
        if (!data) return;
        $('input[name=id]').val(data.id);
        $('select[name=region_id]').val(data.region_id);
        $('input[name=name]').val(data.name);
        $('input[name=address]').val(data.address);
        $('input[name=email]').val(data.email);
        $('input[name=phone]').val(data.phone);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
}
</script>

<?= $this->endSection() ?>
