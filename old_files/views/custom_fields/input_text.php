<?php
if($field_info->id=='6'){
 if(!empty($field_info->value)){ 
        $field_value=$field_info->value; 
    }else{ 
        if(!isset($estimate_location)) { $estimate_location=''; }
        $field_value=$estimate_location;
        }
    }else{
        
        $field_value=esc($field->value ?? ''); //$field_info->value;
    }
echo form_input(array(
    "id" => "custom_field_" . $field_info->id,
    "name" => "custom_field_" . $field_info->id,
    "value" => $field_value,
    "class" => "form-control",
    "placeholder" => $placeholder,
    "data-rule-required" => $field_info->required ? true : "false",
    "data-msg-required" => app_lang("field_required"),
));
