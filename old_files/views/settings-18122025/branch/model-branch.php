<div class="modal-body clearfix">
    <div class="container-fluid">
         <div class="col-md-12">
                <h4>Offices</h4>
                <form id="office-form">
                    <input type="hidden" name="id" id="office-id">
                    <select name="region_id" id="office-region-id" class="form-control mb-2" required>
                        <option value="">Select Region</option>
                        <?php foreach($regions as $r): ?>
                            <option value="<?= $r->id ?>"><?= $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="name" id="office-name" placeholder="Office Name" required class="form-control mb-2">
                    <input type="text" name="code" id="office-code" placeholder="Code" class="form-control mb-2">
                    <button type="submit" class="btn btn-primary btn-block">Save Office</button>
                </form>

                <ul class="list-group mt-3" id="office-list">
                    <div></div>
                   
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= $o->name ?> (<?= $o->region_id ?>)
                            <button class="btn btn-sm btn-danger" onclick="deleteOffice(<?= $o->id ?>)">Delete</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div> 
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>


<script type="text/javascript">
    $('#region-form').submit(function(e){
    e.preventDefault();
    $.post('<?= site_url('settings/add_region') ?>', $(this).serialize(), function(res){
        location.reload();
    });
});
function deleteRegion(id){
    if(confirm('Are you sure?')) $.get('<?= site_url('settings/delete_region') ?>/'+id, function(){ location.reload(); });
}
// ---------- OFFICE ----------
$('#office-form').submit(function(e){
    e.preventDefault();
    $.post('<?= site_url('settings/add_office') ?>', $(this).serialize(), function(res){
        location.reload();
    });
});

function deleteOffice(id){
    if(confirm('Are you sure?')) $.get('<?= site_url('settings/delete_office') ?>/'+id, function(){ location.reload(); });
}
    
// ---------- DIVISION ----------
$('#division-form').submit(function(e){
    e.preventDefault();
    $.post('<?= site_url('settings/add_division') ?>', $(this).serialize(), function(res){
        location.reload();
    });
});

function deleteDivision(id){
    if(confirm('Are you sure?')) $.get('<?= site_url('settings/delete_division') ?>/'+id, function(){ location.reload(); });
}

// ---------- DEPENDENT DROPDOWN ----------
$('#office-region-id').change(function(){
    let region_id = $(this).val();
    $.getJSON('<?= site_url('settings/get_offices_by_region') ?>/'+region_id, function(data){
        let options = '<option value="">Select Office</option>';
        $.each(data, function(i,o){
            options += `<option value="${o.id}">${o.name}</option>`;
        });
        $('#division-office-id').html(options);
    });
});
</script>