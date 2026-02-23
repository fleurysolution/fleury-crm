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
                        <h4><?php echo app_lang('regions'); ?></h4>
                    </div>

                    <form id="region-form" method="post">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Region <span class="label-danger">*</span></label>
                                <input type="text" name="name" required class="form-control">
                                <input type="hidden" name="id">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Region Code</label>
                                <input type="text" name="region_code" class="form-control">
                            </div>

                            <div class="form-group col-sm-8">
                                <label>Description</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                            </div>

                            <div class="form-group col-sm-4">
                                <input type="submit" class="btn btn-md mt-4 btn-success" value="Save">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card p-3">
                    <h4>Manage Regions</h4>

                    <div class="table-responsive">
                        <table id="region-table" class="display clickable b-b-only" cellspacing="0" width="100%">
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
    $('#region-table').DataTable({ pageLength: 10, order: [[0, 'desc']] });

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