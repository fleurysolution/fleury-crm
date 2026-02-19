<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $settingsModel = model('App\Models\Settings_model'); 
            $tab_view['active_tab'] = "roles";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="row">

                 <div class="col-md-12 card">
                    <div class="page-title clearfix">
                            <h4> <?php echo app_lang('branches'); ?></h4>
                        </div>
                            <form action="<?= base_url('settings/save_office'); ?>" id="office-form" method="post">
                              
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label> Region </label>
                                        <select class="form-select" name="region_id">
                                            <option value="">Select</option>
                                             <?php foreach($regions as $regionData): ?>
                                                <option value="<?= $regionData->id ?>"> <?= $regionData->name ?></option>
                                             <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4" >
                                        <label> Branch/Office<span class="label-danger"> *</span>  </label>
                                        <input type="text" name="name" value="" required class="form-control">
                                        <input type="hidden" name="id" value="" >
                                    </div>
                                     <div class="form-group col-sm-4" >
                                        <label> Address  </label>
                                        <input type="text" name="address" value="" class="form-control">
                                    </div>
                                     <div class="form-group col-sm-4" >
                                        <label> Email  </label>
                                        <input type="email" name="email" value="" class="form-control">
                                    </div>
                                    <div class="form-group col-sm-4" >
                                        <label> Phone  </label>
                                        <input type="tel" name="phone" value="" class="form-control">
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="submit" class="btn btn-md mt-4 btn-success" value="Save">
                                    </div>
                                </div>
                            </form>
                        </div>
                 </div>
                <div class="card p-3">
                    <h4>Manage Branches</h4>
               
                    
                    <div class="table-responsiv table-border">
                        <table id="branch-table" class="display clickable thead b-b-only" cellspacing="0" width="100%">  
                            <thead>
                                <tr><th> ID </th><th> Title </th><th> Region </th><th> Address </th> <th> Phone </th>  <th> Email </th> <th> Actions </th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($offices as $d): ?>
                                    <tr data-id="<?= $d->id ?>">
                                    <td> <?= $d->id ?> </td>
                                    <td> <?= $d->name ?> (<?= $d->id ?>) </td>
                                    <td><?php $data=$settingsModel->get_name_data('regions',$d->region_id); print_r($data[0]->name);  ?> </td>
                                    <td> <?= $d->address ?> (<?= $d->id ?>) </td>
                                    <td> <?= $d->phone ?> (<?= $d->id ?>) </td>
                                    <td> <?= $d->email ?> (<?= $d->id ?>) </td>
                                    <td> <!-- <button class="btn btn-sm btn-warning" onclick="editOffice(<?= $d->id ?>)">Edit</button> --> <button class="btn btn-sm btn-danger" onclick="deleteOffice(<?= $d->id ?>)">Delete</button></td></tr>
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
$('#region').change(function() {
    let region_id = $(this).val();
    $('#office').empty();
    $.getJSON('<?= site_url("branch/get_offices_by_region") ?>/' + region_id, function(offices) {
        $('#office').append('<option value="">Select Office</option>');
        $.each(offices, function(i, office) {
            $('#office').append('<option value="'+office.id+'">'+office.name+'</option>');
        });
    });
});

$('#office').change(function() {
    let office_id = $(this).val();
    $('#division').empty();
    $.getJSON('<?= site_url("branch/get_divisions_by_office") ?>/' + office_id, function(divisions) {
        $('#division').append('<option value="">Select Division</option>');
        $.each(divisions, function(i, division) {
            $('#division').append('<option value="'+division.id+'">'+division.name+'</option>');
        });
    });
});

// ---------- OFFICE ----------
/*$('#office-form').submit(function(e){<?php get_uri("settings/model_division"); ?>
    e.preventDefault();
    $.post('<?= site_url('settings/add_office') ?>', $(this).serialize(), function(res){
        location.reload();
    });
});*/
/*
function deleteOffice(id){
    if(confirm('Are you sure?')) $.get('<?= site_url('settings/delete_office') ?>/'+id, function(){ location.reload(); });
}
*/
</script>



<script>
    $(document).ready(function() {
    $('#branch-table').DataTable({
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
    $('#office-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?= site_url('settings/save_office'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('#office-form input[type=submit]').val('Saving...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // reload table (simple)
                } else {
                    alert('Failed to save office.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Server error while saving office.');
            },
            complete: function() {
                $('#office-form input[type=submit]').val('Save').prop('disabled', false);
            }
        });
    });

});


// ✅ Delete office
function deleteOffice(id) {
    if (confirm('Are you sure you want to delete this office?')) {
        $.ajax({
            url: '<?= site_url('settings/delete_office/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('tr[data-id="'+id+'"]').remove();
                    reload();
                } else {
                    alert('Failed to delete office.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                reload();
                alert('Server error while deleting office.');
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