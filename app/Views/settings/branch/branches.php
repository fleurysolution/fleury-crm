<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "settings";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="row">

                <div class="col-md-12 card">
                    <div class="page-title clearfix">
                        <h4><?php echo app_lang('branches'); ?></h4>
                    </div>

                    <form id="office-form" method="post">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Region <span class="label-danger">*</span></label>
                                <select class="form-select" name="region_id" required>
                                    <option value="">Select</option>
                                    <?php foreach ($regions as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Branch/Office <span class="label-danger">*</span></label>
                                <input type="text" name="name" required class="form-control">
                                <input type="hidden" name="id">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>

                            <div class="form-group col-sm-4">
                                <input type="submit" class="btn btn-md mt-4 btn-success" value="Save">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card p-3">
                    <h4>Manage Branches</h4>

                    <div class="table-responsive">
                        <table id="branch-table" class="display clickable thead b-b-only" cellspacing="0" width="100%">
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
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#branch-table').DataTable({ pageLength: 10, order: [[0, 'desc']] });

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