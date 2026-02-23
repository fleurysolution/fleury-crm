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
                        <h4><?php echo app_lang('divisions'); ?></h4>
                    </div>

                    <form id="division-form" method="post">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Region <span class="label-danger">*</span></label>
                                <select class="form-select" name="region_id" id="region" required>
                                    <option value="">Select</option>
                                    <?php foreach ($regions as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Branch/Office <span class="label-danger">*</span></label>
                                <select class="form-select" name="office_id" id="office" required>
                                    <option value="">Select</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Division <span class="label-danger">*</span></label>
                                <input type="text" name="name" required class="form-control">
                                <input type="hidden" name="id">
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
                    <h4>Manage Divisions</h4>

                    <div class="table-responsive">
                        <table id="division-table" class="display clickable b-b-only" cellspacing="0" width="100%">
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
    $('#division-table').DataTable({ pageLength: 10, order: [[0, 'desc']] });

    $('#region').on('change', function() {
        const regionId = $(this).val();
        $('#office').html('<option value="">Select</option>');
        if (!regionId) return;

        $.getJSON('<?= site_url('settings/branches/offices/by-region/'); ?>' + regionId, function(offices) {
            let html = '<option value="">Select</option>';
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