<input type="hidden" name="id" value="<?php echo $model_info->id ?? ''; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group mb-3">
    <div class="row">
        <label for="type" class="col-md-3"><?php echo app_lang('type'); ?></label>
        <div class="col-md-9">
            <?php
            $type = $model_info->type ?? 'organization';
            ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input account_type" type="radio" name="account_type" id="type_organization" value="organization" <?php echo ($type === 'organization') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="type_organization"><?php echo app_lang('organization'); ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input account_type" type="radio" name="account_type" id="type_person" value="person" <?php echo ($type === 'person') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="type_person"><?php echo app_lang('person'); ?></label>
            </div>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="company_name" class="col-md-3 company_name_section"><?php echo ($type === 'person') ? app_lang('name') : app_lang('company_name'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "company_name",
                "name" => "company_name",
                "value" => $model_info->company_name ?? '',
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('company_name'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="address" class="col-md-3"><?php echo app_lang('address'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_textarea(array(
                "id" => "address",
                "name" => "address",
                "value" => $model_info->address ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('address'),
                "style" => "height: 80px"
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="city" class="col-md-3"><?php echo app_lang('city'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "city",
                "name" => "city",
                "value" => $model_info->city ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('city')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="state" class="col-md-3"><?php echo app_lang('state'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "state",
                "name" => "state",
                "value" => $model_info->state ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('state')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="zip" class="col-md-3"><?php echo app_lang('zip'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "zip",
                "name" => "zip",
                "value" => $model_info->zip ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('zip')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="country" class="col-md-3"><?php echo app_lang('country'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "country",
                "name" => "country",
                "value" => $model_info->country ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('country')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="phone" class="col-md-3"><?php echo app_lang('phone'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "phone",
                "name" => "phone",
                "value" => $model_info->phone ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('phone')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="website" class="col-md-3"><?php echo app_lang('website'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "website",
                "name" => "website",
                "value" => $model_info->website ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('website')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <div class="row">
        <label for="vat_number" class="col-md-3"><?php echo app_lang('vat_number'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "vat_number",
                "name" => "vat_number",
                "value" => $model_info->vat_number ?? '',
                "class" => "form-control",
                "placeholder" => app_lang('vat_number')
            ));
            ?>
        </div>
    </div>
</div>

<?php if (isset($currency_dropdown)) { ?>
    <div class="form-group mb-3">
        <div class="row">
            <label for="currency" class="col-md-3"><?php echo app_lang('currency'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_input(array(
                    "id" => "currency",
                    "name" => "currency",
                    "value" => $model_info->currency ?? '',
                    "class" => "form-control",
                    "placeholder" => app_lang('currency')
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();

        $('.account_type').click(function() {
            var inputValue = $(this).attr("value");
            if (inputValue === "person") {
                $(".company_name_section").html("<?php echo app_lang('name'); ?>");
                $(".company_name_input_section").attr("placeholder", "<?php echo app_lang('name'); ?>");
            } else {
                $(".company_name_section").html("<?php echo app_lang('company_name'); ?>");
                $(".company_name_input_section").attr("placeholder", "<?php echo app_lang('company_name'); ?>");
            }
        });
        
        <?php if (isset($currency_dropdown)) { ?>
             $("#currency").select2({
                data: <?php echo json_encode($currency_dropdown); ?>
            });
        <?php } ?>
    });
</script>
