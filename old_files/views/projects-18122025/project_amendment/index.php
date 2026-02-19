<!-- Projedt amendments indez -->
<div class="card">
    <div class="card-header">
        <h6 class="float-start"><?php echo app_lang('project_amendments'); ?></h6>
        <?php
        if ($can_add_remove_project_members) {
            echo modal_anchor(get_uri("projects/project_amendments_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_amendments'), array("class" => "btn btn-default float-end add-member-button", "title" => app_lang('add_amendments'), "data-post-project_id" => $project_id));
        }
        ?>
    </div>

    <div class="table-responsive">
        <table id="project-amendment-table" class="b-b-only no-thead" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#project-amendment-table").appTable({
            source: '<?php echo_uri("projects/project_amendment_list_data/" . $project_id) ?>',
            hideTools: true,
            displayLength: 500,
            columns: [
                {title: ''},
                {title: 'Reason'},
                {title: 'Amended Price'},
                {title: 'amendment Date'},
                {title: '', "class": "text-center option w100"}
            ]
        });
    });
</script>
