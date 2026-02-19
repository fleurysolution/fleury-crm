
<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        

        <div class="col-sm-9 col-lg-10">
            <div class="card">
                <div class="page-title clearfix">
                    <h4> Package Price</h4>
                    <div class="title-button-group">
                        <?php echo modal_anchor(get_uri("package_price/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> Add Package", array("class" => "btn btn-default", "title" =>'Add Package')); ?>
                    </div>
                   
                </div>
                <div class="table-responsive">
                    <table id="category-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#category-table").appTable({
            source: '<?php echo_uri("package_price/list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("parent_category") ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>