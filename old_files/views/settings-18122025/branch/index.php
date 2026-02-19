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

                <div class="col-md-12">
                    <div id="role-list-box" class="card">
                        <div class="page-title clearfix">
                            <h4> <?php echo app_lang('Divisions'); ?></h4>
                            <div class="title-button-group">
                                <?php echo modal_anchor(get_uri("settings/model_division"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_role'), array("class" => "btn btn-default", "title" => app_lang('Divisions'))); ?>
                            </div>
                            
                        </div>
                       
                    </div>
                </div>
                <div class="card p-3">
                    <h4>Manage Branch Structure</h4>
               
                    <div class="form-group">
                        <label>Region</label>
                        <select id="region" class="form-control">
                            <option value="">Select Region</option>
                            <?php foreach($regions as $region): ?>
                                <option value="<?= $region->id ?>"><?= $region->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Office</label>
                        <select id="office" class="form-control"></select>
                    </div>

                    <div class="form-group">
                        <label>Division</label>
                        <select id="division" class="form-control"></select>
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
</script>
