<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $settingsModel = model('App\Models\Settings_model'); 
            $tab_view['active_tab'] = "settings";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="row">

                 <div class="col-md-12 card">
                    <div class="page-title clearfix">
                            <h4> <?php echo app_lang('regions'); ?></h4>
                        </div>
                            <form action="<?= base_url('settings/save_region'); ?>" id="region-form" method="post">
                              
                                <div class="row">
                                   
                                    <div class="form-group col-sm-6" >
                                        <label> Region  </label>
                                        <input type="text" name="name" value="" required class="form-control">
                                        <input type="hidden" name="id" value="" >
                                    </div>
                                     <div class="form-group col-sm-6" >
                                        <label> Region Code  </label>
                                        <input type="text" name="region_code" value="" class="form-control">
                                    </div>
                                     <div class="form-group col-sm-4" >
                                        <label> Description  </label>
                                        <textarea  name="description" rows="4" maxlength="50" class="form-control"> </textarea>
                                    </div>
                                    
                                    <div class="form-group col-sm-4">
                                        <input type="submit" class="btn btn-md mt-4 btn-success" value="Save">
                                    </div>
                                </div>
                            </form>
                        </div>
                 </div>
                <div class="card p-3">
                    <h4>Manage Regions</h4>
                   
                    <div class="table-responsiv">
                        <table id="region-table" class="display clickable b-b-only" cellspacing="0" width="100%">  
                            <thead>
                                <tr><th> ID </th><th> Title </th> <th> Region code </th> <th> Description </th>  <th> Actions </th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($regions as $d): ?>
                                <tr><td> <?= $d->id ?></td><td> <?= $d->name ?></td><td> <?= $d->code ?></td><td> <?= $d->description ?></td><td> <button class="btn btn-sm btn-danger" onclick="deleteDivision(<?= $d->id ?>)">Delete</button></td></tr>
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
    $('#region-table').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        language: {
            search: "Filter records:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ offices"
        }
    });
});
$(document).ready(function() {

    // ✅ Save office form
    $('#region-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?= site_url('settings/save_region'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('#Region-form input[type=submit]').val('Saving...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // reload table (simple)
                } else {
                    alert('Failed to save Region.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Server error while saving Region.');
            },
            complete: function() {
                $('#office-form input[type=submit]').val('Save').prop('disabled', false);
            }
        });
    });

});


// ✅ Delete office
function deleteOffice(id) {
    if (confirm('Are you sure you want to delete this Region?')) {
        $.ajax({
            url: '<?= site_url('settings/delete_region/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('tr[data-id="'+id+'"]').remove();
                    reload();
                } else {
                    alert('Failed to delete Region.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                reload();
                alert('Server error while deleting Region.');
            }
        });
    }
}


// ✅ Edit office (simple inline loader)
function editOffice(id) {
    $.ajax({
        url: '<?= site_url('settings/get_office'); ?>/' + id, // optional endpoint
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data) {
                $('input[name=id]').val(data.id);
                $('select[name=region_id]').val(data.region_id);
                $('input[name=name]').val(data.name);
                $('input[name=address]').val(data.address);
                $('input[name=email]').val(data.email);
                $('input[name=phone]').val(data.phone);
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
}
</script>