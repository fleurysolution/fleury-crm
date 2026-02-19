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
                            <h4> <?php echo app_lang('divisions'); ?></h4>
                        </div>
                            <form action="<?= base_url('settings/save_division'); ?>" id="division-form" method="post">
                              
                                <div class="row">
                                    <div class="form-group col-sm-4"> <!-- onchange="getbranchData(this.value);" -->
                                        <label> Region <span class="label-danger"> *</span></label>
                                        <select class="form-select" name="region_id" id="region" >
                                            <option value="">Select</option>
                                             <?php foreach($regions as $regionData): ?>
                                                <option value="<?= $regionData->id ?>"> <?= $regionData->name ?></option>
                                             <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label> Branch/Office <span class="label-danger"> *</span></label>
                                        <select class="form-select" name="office_id" id="office">
                                            <option value="">Select</option>
                                           <!--  -->
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4" >
                                        <label> Division  </label>
                                        <input type="text" name="name" value="" required class="form-control">
                                        <input type="hidden" name="id" value="" >
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
                    <h4>Manage Divisions</h4>
               
                    
                    <div class="table-responsiv">
                        <table id="division-table" class="display clickable b-b-only" cellspacing="0" width="100%">  
                            <thead>
                                <tr><th> ID </th><th> Title </th><th> Region </th> <th> Branch </th><th> Description </th><th> Actions </th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($divisions as $d): ?>
                                <tr><td><?= $d->office_id ?> </td>
                                    <td> <?= $d->name ?> </td>
                                    <td><?php $data=$settingsModel->get_name_data('regions',$d->region_id); print_r($data[0]->name);  ?> </td>
                                    <td><?php $dataOffices=$settingsModel->get_name_data('offices',$d->office_id); print_r($dataOffices[0]->name);  ?> </td>
                                    <td><?= $d->description ?> </td>
                                    <td> <button class="btn btn-sm btn-danger" onclick="deleteDivision(<?= $d->id ?>)">Delete</button></td></tr>
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
    $('#division-table').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        language: {
            search: "Filter records:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ offices"
        }
    });
});
$('#region').change(function() {
    let region_id = $(this).val();
    $('#office').empty();
    $.getJSON('<?= site_url("settings/get_offices_by_regionID") ?>/' + region_id, function(offices) {
        $('#office').append('<option value="">Select Office</option>');
        $.each(offices, function(i, office) {
            $('#office').append('<option value="'+office.id+'">'+office.name+'</option>');
        });
    });
});

$('#office').change(function() {
    let office_id = $(this).val();
    $('#division').empty();
    $.getJSON('<?= site_url("settings/get_divisions_by_office") ?>/' + office_id, function(divisions) {
        $('#division').append('<option value="">Select Division</option>');
        $.each(divisions, function(i, division) {
            $('#division').append('<option value="'+division.id+'">'+division.name+'</option>');
        });
    });
});

</script>

<script>
    $(document).ready(function() {
    $('#division-table').DataTable({
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
    $('#division-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?= site_url('settings/save_division'); ?>',
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
                    alert('Failed to save Division.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Server error while saving Division.');
            },
            complete: function() {
                $('#office-form input[type=submit]').val('Save').prop('disabled', false);
            }
        });
    });

});


// ✅ Delete office
function deleteDivision(id) {
    if (confirm('Are you sure you want to delete this Division?')) {
        $.ajax({
            url: '<?= site_url('settings/delete_division/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('tr[data-id="'+id+'"]').remove();
                    reload();
                } else {
                    alert('Failed to delete Division.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                reload();
                alert('Server error while deleting Division.');
            }
        });
    }
}


// ✅ Edit office (simple inline loader)
function editDivision(id) {
    $.ajax({
        url: '<?= site_url('settings/get_Division'); ?>/' + id, // optional endpoint
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