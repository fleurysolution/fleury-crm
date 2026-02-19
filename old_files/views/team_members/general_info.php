<div class="tab-content">
    <style>
        input[type="text"] {
            width: 200px;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            display: none;
        }
    </style>
    <?php echo form_open(get_uri("team_members/save_general_info/" . $user_info->id), array("id" => "general-info-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
    <div class="card border-top-0 rounded-top-0">
        <div class=" card-header">
            <h4> <?php echo app_lang('general_info'); ?></h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label for="first_name" class=" col-md-2"><?php echo app_lang('first_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "first_name",
                            "name" => "first_name",
                            "value" => $user_info->first_name,
                            "class" => "form-control",
                            "placeholder" => app_lang('first_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="last_name" class=" col-md-2"><?php echo app_lang('last_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "last_name",
                            "name" => "last_name",
                            "value" => $user_info->last_name,
                            "class" => "form-control",
                            "placeholder" => app_lang('last_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <p><strong>  <?php echo app_lang('mailing_address'); ?></strong></p>
                <div class="row">
                    <label for="address" class=" col-md-2"><?php echo app_lang('address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        /*echo form_textarea(array(
                            "id" => "address",
                            "name" => "address",
                            "value" => $user_info->address,
                            "class" => "form-control",
                            "placeholder" => app_lang('address')
                        ));*/

                        echo form_input(array(
                            "id" => "address",
                            "name" => "address",
                            "value" => $user_info->address,
                            "class" => "form-control",
                            "placeholder" => app_lang('address')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3 col-md-3">
                    <div class="form-group">  
                    <div class="row">                  
                        <label for="last_name" class=" col-md-4"><?php echo app_lang('city'); ?></label>
                        <div class=" col-md-8">
                            <?php
                            echo form_input(array(
                                "id" => "permanent_city",
                                "name" => "permanent_city",
                                "class" => "form-control",
                                "value" => $user_info->permanent_city,
                                "placeholder" => app_lang('city'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="col-sm-3 col-md-3">
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class=" col-md-4"><?php echo app_lang('state'); ?></label>
                            <div class=" col-md-8">
                                <?php
                                echo form_input(array(
                                    "id" => "permanent_state",
                                    "name" => "permanent_state",
                                    "class" => "form-control",
                                    "value" => $user_info->permanent_state,
                                    "placeholder" => app_lang('state'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3">
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class=" col-md-4"><?php echo app_lang('zip'); ?></label>
                            <div class=" col-md-8">
                                <?php
                                echo form_input(array(
                                    "id" => "permanent_zipcode",
                                    "name" => "permanent_zipcode",
                                    "class" => "form-control",
                                    "value" => $user_info->permanent_zipcode,
                                    "placeholder" => app_lang('zip'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3">
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class=" col-md-4"><?php echo app_lang('country'); ?></label>
                            <div class=" col-md-8">
                                <?php
                                echo form_input(array(
                                    "id" => "permanent_country",
                                    "name" => "permanent_country",
                                    "class" => "form-control",
                                    "value" => $user_info->permanent_country,
                                    "placeholder" => app_lang('country'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                

            <div class="form-group">
                
                <p><strong>  <?php echo app_lang('alternative_address'); ?></strong></p>
            <label>
                <input type="checkbox" id="same_as_mailing_address" /> 
                <?php echo app_lang('same_as_mailing_address'); ?>
            </label>

                
                <div class="row">
                    <label for="alternative_address" class=" col-md-2"><?php echo app_lang('address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        /*echo form_textarea(array(
                            "id" => "alternative_address",
                            "name" => "alternative_address",
                            "value" => $user_info->alternative_address,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_address')
                        ));*/
                        ?>
                        <?php
                        echo form_input(array(
                            "id" => "alternative_address",
                            "name" => "alternative_address",
                            "value" => $user_info->alternative_address,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_address')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3 col-md-3">
                    <div class="form-group">  
                    <div class="row">                  
                        <label for="last_name" class=" col-md-4"><?php echo app_lang('city'); ?></label>
                        <div class=" col-md-8">
                            <?php
                            echo form_input(array(
                                "id" => "alternative_city",
                                "name" => "alternative_city",
                                "class" => "form-control",
                                "value" => $user_info->alternative_city,
                                "placeholder" => app_lang('city'),
                            ));
                            ?>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="col-sm-3 col-md-3">
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class=" col-md-4"><?php echo app_lang('state'); ?></label>
                            <div class=" col-md-8">
                                <?php
                                echo form_input(array(
                                    "id" => "alternative_state",
                                    "name" => "alternative_state",
                                    "class" => "form-control",
                                    "value" => $user_info->alternative_state,
                                    "placeholder" => app_lang('state'),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3">
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class=" col-md-4"><?php echo app_lang('zip'); ?></label>
                            <div class=" col-md-8">
                                <?php
                                echo form_input(array(
                                    "id" => "alternative_zipcode",
                                    "name" => "alternative_zipcode",
                                    "class" => "form-control",
                                    "value" => $user_info->alternative_zipcode,
                                    "placeholder" => app_lang('zip'),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3">
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class=" col-md-4"><?php echo app_lang('country'); ?></label>
                            <div class=" col-md-8"> 
                                <?php
                                echo form_input(array(
                                    "id" => "alternative_country",
                                    "name" => "alternative_country",
                                    "class" => "form-control",
                                    "value" => $user_info->alternative_country,
                                    "placeholder" => app_lang('country'),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="phone" class=" col-md-2"><?php echo app_lang('phone'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "phone",
                            "name" => "phone",
                            "value" => $user_info->phone,
                            "class" => "form-control",
                            "placeholder" => '(555) 123-4567',
                            "maxlength"=>'18'
                        ));
                        ?>

                    </div>
                    <div class="error" id="error-phone">Please enter a valid phone number.</div>

                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="alternative_phone" class=" col-md-2"><?php echo app_lang('alternative_phone'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "alternative_phone",
                            "name" => "alternative_phone",
                            "value" => $user_info->alternative_phone,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_phone'),
                            "maxlength"=>'14'
                        ));
                        ?>
                    </div>
                        <div class="error" id="error-alternative_phone">Please enter a valid alternative phone number.</div>

                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="skype" class=" col-md-2">Skype</label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "skype",
                            "name" => "skype",
                            "value" => $user_info->skype ? $user_info->skype : "",
                            "class" => "form-control",
                            "placeholder" => "Skype"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="dob" class=" col-md-2"><?php echo app_lang('date_of_birth'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        $min_date = date('Y-m-d', strtotime('-1 years'));

                        echo form_input(array(
                            "id" => "dob",
                            "name" => "dob",
                            "value" => $user_info->dob,
                            "class" => "form-control",
                            "placeholder" => app_lang('date_of_birth'),
                            "autocomplete" => "off",
                             "max" => $min_date
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="ssn" class=" col-md-2"><?php echo app_lang('ssn'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "ssn",
                            "name" => "ssn",
                            "value" => mask_string($user_info->ssn),
                            "class" => "form-control",
                            "placeholder" => app_lang('ssn')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="gender" class=" col-md-2"><?php echo app_lang('gender'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_radio(array(
                            "id" => "gender_male",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "male", ($user_info->gender === "male") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> 
                        <?php
                        echo form_radio(array(
                            "id" => "gender_female",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "female", ($user_info->gender === "female") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_female" class="p0 mr15"><?php echo app_lang('female'); ?></label>
                        <?php
                        echo form_radio(array(
                            "id" => "gender_other",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "other", ($user_info->gender === "other") ? true : false);
                        ?>
                        <label for="gender_other" class=""><?php echo app_lang('other'); ?></label>
                    </div>
                </div>
            </div>


            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-2", "field_column" => " col-md-10")); ?> 

        </div>
        <div class="card-footer rounded-0">
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
        function setupPhoneInput(inputId) {
            const phoneInput = document.getElementById(inputId);
            const maxLength = phoneInput.getAttribute('maxlength'); // Get maxlength from the attribute

            phoneInput.addEventListener('input', function () {
                // Allow only digits
                this.value = this.value.replace(/\D/g, '');

                // Limit to maxLength digits
                if (this.value.length > maxLength) {
                    this.value = this.value.slice(0, maxLength); // Limit to maxLength digits
                }

                // Format the phone number
                if (this.value.length > 6) {
                    this.value = `(${this.value.slice(0, 3)}) ${this.value.slice(3, 6)}-${this.value.slice(6, maxLength)}`;
                } else if (this.value.length > 3) {
                    this.value = `(${this.value.slice(0, 3)}) ${this.value.slice(3)}`;
                } else if (this.value.length > 0) {
                    this.value = `(${this.value}`;
                }
            });

            // Prevent pasting non-numeric characters
            phoneInput.addEventListener('paste', function (e) {
                e.preventDefault();
            });
        }

        // Setup both phone inputs
        setupPhoneInput('phone');
        setupPhoneInput('alternative_phone');

        document.getElementById('same_as_mailing_address').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('alternative_address').value = document.getElementById('address').value;
            document.getElementById('alternative_city').value = document.getElementById('permanent_city').value;
            document.getElementById('alternative_state').value = document.getElementById('permanent_state').value;
            document.getElementById('alternative_zipcode').value = document.getElementById('permanent_zipcode').value;
            document.getElementById('alternative_country').value = document.getElementById('permanent_country').value;
        } else {
            document.getElementById('alternative_address').value = '';
            document.getElementById('alternative_city').value = '';
            document.getElementById('alternative_state').value = '';
            document.getElementById('alternative_zipcode').value = '';
            document.getElementById('alternative_country').value = '';
        }
    });
    </script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#general-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                setTimeout(function () {
                    window.location.href = "<?php echo get_uri("team_members/view/" . $user_info->id); ?>" + "/general";
                }, 500);
            }
        });
        $("#general-info-form .select2").select2();

        setDatePicker("#dob");

    });
</script>    