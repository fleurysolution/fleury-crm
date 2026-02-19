<div class="tab-content">
    <?php
    $url = "team_members";
    $show_submit = true;
    if ($user_info->user_type === "client") {
        $url = "clients";
        if (isset($can_edit_clients) && !$can_edit_clients) {
            $show_submit = false;
        }
    }
    if(!empty($subscription_data)){
    ?>
    <div class="card border-top-0 rounded-top-0">
        <div class=" card-header">
            <h4><?php echo app_lang('subscription'); 
            $cancelbtn='subscriptions/update_subscription_status/'.$subscription_data[0]->id.'/cancelled';
        ?></h4>
        </div>
        <div class="card-body">
            <input type="hidden" name="subscription_id" value="<?php echo $subscription_data[0]->id; ?>" />
            <input type="hidden" name="stripe_product_id" value="<?php echo $subscription_data[0]->stripe_subscription_id; ?>" />
            <input type="hidden" name="subscription_price_id" value="<?php echo $subscription_data[0]->stripe_product_id; ?>" />
            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-2"><?php echo app_lang('Service_id'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "service_product_id",
                            "name" => "service_product_id",
                            "value" => $subscription_data[0]->stripe_product_id,
                            "class" => "form-control",
                            "autocomplete" => "off",
                            "data-msg-required" => app_lang("field_required"),
                            "readonly" => true,
                        ));
                        ?>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-2"><?php echo app_lang('subscription_id'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "subscription_id",
                            "name" => "subscription_id",
                            "value" => $subscription_data[0]->stripe_subscription_id,
                            "class" => "form-control",
                            "autocomplete" => "off",
                            "data-msg-required" => app_lang("field_required"),
                            "readonly" => true,
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-2"><?php echo app_lang('service_title'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "item_title",
                            "name" => "item_title",
                            "value" => $subscription_item_data[0]->title,
                            "class" => "form-control",
                            "autocomplete" => "off",
                            "data-msg-required" => app_lang("field_required"),
                            "readonly" => true,
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-2"><?php echo app_lang('status'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "item_title",
                            "name" => "item_title",
                            "value" => $subscription_data[0]->status,
                            "class" => "form-control",
                            "autocomplete" => "off",
                            "data-msg-required" => app_lang("field_required"),
                            "readonly" => true,
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-2"><?php echo app_lang('amount'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "rate",
                            "name" => "rate",
                            "value" => '$'.$subscription_item_data[0]->rate,
                            "class" => "form-control",
                            "autocomplete" => "off",
                            "data-msg-required" => app_lang("field_required"),
                            "readonly" => true,
                        ));
                        ?>
                    </div>
                </div>
            </div>

             <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-2"><?php echo app_lang('total'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "rate",
                            "name" => "rate",
                            "value" => '$'.$subscription_item_data[0]->total,
                            "class" => "form-control",
                            "autocomplete" => "off",
                            "data-msg-required" => app_lang("field_required"),
                            "readonly" => true,
                        ));
                        ?>
                    </div>
                </div>
            </div>



            
        </div>
        <div class=" card-header">

            <a href="javascript:void(0);" class="btn button btn-primary"><?php echo app_lang("upgrade"); ?></a>
            <a href="<?php echo base_url($cancelbtn); ?>" class="btn button btn-primary"><?php echo app_lang("cancel"); ?></a>
       </div>
       <?php }else{  ?>

       <div class=" card-header">
            <h4><?php echo 'No Subscription Found'; 
        ?></h4>
    </div>
<?php } ?>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#account-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });
        $("#account-info-form .select2").select2();


        //show/hide asmin permission help message
        $("#user-role").change(function () {
            if ($(this).val() === "admin") {
                $("#user-role-help-block").removeClass("hide");
            } else {
                $("#user-role-help-block").addClass("hide");
            }
        });

        //show/hide disable login help message
        $("#disable_login").click(function () {
            if ($(this).is(":checked")) {
                $("#disable-login-help-block").removeClass("hide");
            } else {
                $("#disable-login-help-block").addClass("hide");
            }
        });

        //show/hide user status help message
        $("#user_status").click(function () {
            if ($(this).is(":checked")) {
                $("#user-status-help-block").removeClass("hide");
            } else {
                $("#user-status-help-block").addClass("hide");
            }
        });

        //the checkbox will be enable if anyone enter the password
        $("#password").change(function () {
            var password = $("#password").val();
            if (password) {
                $("#resend_login_details_section").removeClass("hide");
            } else {
                $("#resend_login_details_section").addClass("hide");
            }
        });
    });
</script>    