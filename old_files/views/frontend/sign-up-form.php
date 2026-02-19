<?php include('includes/header.php'); ?>
<style>
    .danger{
        color:red;
    }
    .success{
        color:green;
    }
</style>
    <section id="faq" class="faq section light-background mt-5">

      <div class="container-fluid mt-5">

        <div class="row gy-4">    
            <div id="page-content" class="clearfix">
                <div class="scrollable-page row">
                    <div class="form-signin col-md-6">
                        <div class="card bg-white clearfix">
                            <div class="card-header text-center">
                                <h2 class="form-signin-heading"><?php echo app_lang('signup'); ?></h2>
                                <p><?php //echo $signup_message; ?> Enter the Details below. These credentials will be used to login your CRM and it will be used to login bpms247.com.</p>
                                <p><b> Note: All <span class="danger">*</span> fields are required </b> </p>
                            </div>
                            <div class="card-body p30 rounded-bottom">
                                <?php
                                $action_url = "front/create_account";
                                echo form_open($action_url, array("id" => "signup-form", "class" => "general-form", "role" => "form"));
                                ?>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name" class="col-md-12"><?php echo app_lang('first_name'); ?><span class="danger">*</span></label>
                                        <div class="col-md-12">
                                            <?php
                                            echo form_input(array(
                                                "id" => "first_name",
                                                "name" => "first_name",
                                                "class" => "form-control",
                                                "autofocus" => true,
                                                "required"=>true,
                                                "data-rule-required" => true,
                                                "data-msg-required" => app_lang("field_required"),
                                            ));
                                            ?>
                                            <input type="hidden" name="signup_key"  value="<?php echo isset($signup_key) ? $signup_key : ''; ?>" />
                                            <input type="hidden" name="role_id"  value="<?php echo isset($role_id) ? $role_id : ''; ?>" />
                                            <input type="hidden" name="package_id"  value="<?php echo isset($id) ? $id : ''; ?>" />
                                        </div>
                                    </div>

                                    

                                    <div class="form-group  col-md-6">
                                        <label for="last_name" class="col-md-12"><?php echo app_lang('last_name'); ?></label>
                                        <div class="col-md-12">
                                            <?php
                                            echo form_input(array(
                                                "id" => "last_name",
                                                "name" => "last_name",
                                                "class" => "form-control",
                                                "data-rule-required" => true,
                                                "data-msg-required" => app_lang("field_required"),
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                       <div class="form-group">
                                            <div class="row">
                                                <label for="account_type" class="col-md-12"><?php echo app_lang('type'); ?></label>
                                                <div class="col-md-12">
                                                    <?php
                                                    echo form_radio(array(
                                                        "id" => "type_organization",
                                                        "name" => "account_type",
                                                        "class" => "form-check-input account-type",
                                                        "data-msg-required" => app_lang("field_required"),
                                                            ), "organization", true);
                                                    ?>
                                                    <label for="type_organization" class="mr15"><?php echo app_lang('organization'); ?></label>
                                                    <?php
                                                    echo form_radio(array(
                                                        "id" => "type_person",
                                                        "name" => "account_type",
                                                        
                                                        "class" => "form-check-input account-type",
                                                        "data-msg-required" => app_lang("field_required"),
                                                            ), "person", false);
                                                    ?>
                                                    <label for="type_person" class=""><?php echo app_lang('individual'); ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group company-name-section">
                                            <label for="company_name" class="col-md-12"><?php echo app_lang('company_name'); ?></label>
                                            <div class="col-md-12">
                                                <?php
                                                echo form_input(array(
                                                    "id" => "company_name",
                                                    "name" => "company_name",
                                                    "class" => "form-control",
                                                ));
                                                ?>
                                            </div>
                                        </div>

                                    
                                <div class="row">
                                    <?php if ($signup_type === "new_client") { ?>
                                        <div class="form-group col-md-6">
                                            <label for="email" class="col-md-12"><?php echo app_lang('email'); ?><span class="danger">*</span></label>
                                            <div class="col-md-12">
                                                <?php
                                                echo form_input(array(
                                                    "id" => "email",
                                                    "name" => "email",
                                                    "class" => "form-control",
                                                    "autofocus" => true,
                                                    "data-rule-email" => true,
                                                    "required"=>true,
                                                    "data-msg-email" => app_lang("enter_valid_email"),
                                                    "data-rule-required" => true,
                                                    "data-msg-required" => app_lang("field_required"),
                                                ));
                                                ?>
                                                <p class="danger" id="email-validation-message"></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group col-md-6">
                                        <label for="sub-domain" title="Note: Only small letter characters allowed. No special chars, No space, No numbers allowed. "> Sub Domain Name<span class="danger">*</span></label>
                                        <div class="col-md-12">
                                        <input type="text" name="subdomain" id="subdomain" class="form-control" required  title="Note: Only small letter characters allowed. No special chars, No space, No numbers allowed. ">
                                        <p class="success"> Note: Only small letter characters allowed. No special chars, No space, No numbers allowed. </p>
                                        <p class="danger" id="subdomain-validation-message" onkeyup="validateInput(subdomain);"></p>

                                        </div>
                                    </div>
                                </div>
                                    
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="password" class="col-md-12"><?php echo app_lang('password'); ?><span class="danger">*</span></label>
                                        <div class="col-md-12">
                                            <?php
                                            echo form_password(array(
                                                "id" => "password",
                                                "name" => "password",
                                                "class" => "form-control",
                                                "data-rule-required" => true,
                                                "data-msg-required" => app_lang("field_required"),
                                                "data-rule-minlength" => 6,
                                                "required"=>true,
                                                "data-msg-minlength" => app_lang("enter_minimum_6_characters"),
                                                "autocomplete" => "off",
                                                "style" => "z-index:auto;"
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="retype_password" class="col-md-12"><?php echo app_lang('retype_password'); ?><span class="danger">*</span></label>
                                        <div class="col-md-12">
                                            <?php
                                            echo form_password(array(
                                                "id" => "retype_password",
                                                "name" => "retype_password",
                                                "class" => "form-control",
                                                "required"=>true,
                                                "autocomplete" => "off",
                                                "style" => "z-index:auto;",
                                                "data-rule-equalTo" => "#password",
                                                "data-msg-equalTo" => app_lang("enter_same_value")
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                    <?php if (get_setting("enable_gdpr") && get_setting("show_terms_and_conditions_in_client_signup_page") && get_setting("gdpr_terms_and_conditions_link")) { ?>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="i_accept_the_terms_and_conditions">
                                                    <?php
                                                    echo form_checkbox("i_accept_the_terms_and_conditions", "1", false, "id='i_accept_the_terms_and_conditions' class='float-start form-check-input' data-rule-required='true' data-msg-required='" . app_lang("field_required") . "'");
                                                    ?>    
                                                    <span class="ml10"><?php echo app_lang('i_accept_the_terms_and_conditions') . " " . anchor(get_setting("gdpr_terms_and_conditions_link"), app_lang("gdpr_terms_and_conditions") . ".", array("target" => "_blank")); ?> </span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>

                                <?php if ($signup_type !== "verify_email") { ?>
                                    <div class="col-md-12">
                                        <?php echo view("signin/re_captcha"); ?>
                                    </div>
                                <?php } ?>

                                <div class="form-group mt-2">
                                    <div class="col-md-12">
                                        <button class="w-100 btn btn-medium btn-primary" type="submit" id="submitBtn"><?php echo $signup_type == "send_verify_email" ? app_lang("get_started") : app_lang('signup'); ?></button>
                                        <p> On clicking on get started button it is consent to be agree to register on CRM, receive notifications, and consent to promotional messages via email, SMS, and other communication channels.</p>
                                    </div>
                                </div>

                                <?php echo form_close(); ?>
                                <?php app_hooks()->do_action('app_hook_signup_extension'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                    <img src="<?php echo base_url('assets/images/4957136_4957136.jpg'); ?>" class="img-fluid">
                    </div>

                </div>
            </div> <!-- /container -->
          </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script type="text/javascript">

        $(document).ready(function () {
            $("#email").on("change", function () {
                let email = $(this).val();

                if (email !== "") {
                    $.ajax({
                        url: "<?= base_url('front/email_check') ?>", // Route to the controller method
                        type: "POST",
                        data: { email: email },
                        success: function (response) {
                            console.log(response);
                            if (response === "exists") {
                                $("#email-validation-message")
                                    .text("❌ Email is already registered!")
                                    .removeClass("success")
                                    .addClass("error"); 
                                $("#email").val(""); // Empty the email field
                                $("#submitBtn").prop("disabled", true);
                            } else {
                                $("#email-validation-message")
                                    .text("✅ Email is available!")
                                    .removeClass("error")
                                    .addClass("success");
                                $("#submitBtn").prop("disabled", false); // Enable submit button

                            }
                        },
                        error: function () {
                            $("#email-validation-message").text("Error checking email. Try again.");
                        }
                    });
                }
            });
            function validateInput(subdomain) {
                  const regex = /^[a-z]*$/; 
                  const value = input.value;

                  if (!regex.test(value)) {
                    document.getElementById("error").innerText = "Only lowercase letters (a-z) allowed. No spaces, numbers, or special characters.";
                    input.value = value.replace(/[^a-z]/g, ''); // Remove invalid characters
                  } else {
                    document.getElementById("error").innerText = "";
                  }
                }

            $("#subdomain").on("change", function () {
                const regex = /^[a-z]*$/; 
                let subdomain = $(this).val();   
                  if (!regex.test(subdomain)) { 
                    
                    $("#subdomain-validation-message").text(subdomain+ ": Only lowercase letters (a-z) allowed. No spaces, numbers, or special characters.");
                                $("#subdomain").val(""); // Empty the email field
                                $("#submitBtn").prop("disabled", true);
                     $("#subdomain").value = subdomain.replace(/[^a-z]/g, '');
                  }else{            
                if (email !== "") {
                    $.ajax({
                        url: "<?= base_url('front/subdomain_check') ?>", // Route to the controller method
                        type: "POST",
                        data: { subdomain: subdomain },
                        success: function (response) {
                            console.log(response);
                            if (response === "exists") {
                                $("#subdomain-validation-message").text(subdomain+ " Sub domain is already registered!");
                                $("#subdomain").val(""); // Empty the email field
                                $("#submitBtn").prop("disabled", true);
                            } else {
                                $("#subdomain-validation-message")
                                    .text("✅ Good choice! Sub domain is available!")
                                    .removeClass("error")
                                    .addClass("success");
                                $("#submitBtn").prop("disabled", false); // Enable submit button

                            }
                        },
                        error: function () {
                            $("#email-validation-message").text("Error checking email. Try again.");
                        }
                    });
                }
                }
            });
        });


            $(document).ready(function () {
                $("#signup-form").appForm({
                    isModal: false,
                    onSubmit: function () {
                        appLoader.show();
                    },
                    onSuccess: function (result) {
                        appLoader.hide();
                        appAlert.success(result.message, {container: '.card-body', animate: false});
                        $("#signup-form").remove();

<?php if ($signup_type !== "send_verify_email") { ?>
                            $("#signin_link").remove();
<?php } ?>
                    },
                    onError: function (result) {
                        appLoader.hide();
                        appAlert.error(result.message, {container: '.card-body', animate: false});
                        return false;
                    }
                });

                $('.account-type').click(function () {
                    var inputValue = $(this).attr("value");
                    if (inputValue === "person") {
                        $(".company-name-section").addClass("hide");
                    } else {
                        $(".company-name-section").removeClass("hide");
                    }
                });
            });
        </script>    
  
    <?php include('includes/footer.php'); ?>