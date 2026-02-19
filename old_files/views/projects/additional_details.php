<?php 
if($project_info->region_id || $project_info->office_id || $project_info->division_id){ 
    $this->Settings_model = model('App\Models\Settings_model'); 
        $regionName=$this->Settings_model->get_name_data('regions',$project_info->region_id);
        $OfficeName=$this->Settings_model->get_name_data('offices',$project_info->office_id);
        $divisionsName=$this->Settings_model->get_name_data('divisions',$project_info->division_id);
        
 ?>
        <div class="card">
            <div class="row">
            <div class="pnel-body no-padding col-sm-4">
               <div class='p10'><i data-feather='box' class='icon-16'></i> Region  </div>
               <div class='p10 pt0 b-b ml15'><?php echo $regionName[0]->name; ?></div>
            </div>
            <div class="pnel-body no-padding  col-sm-4">
               <div class='p10'><i data-feather='box' class='icon-16'></i> Office   </div>
               <div class='p10 pt0 b-b ml15'><?php echo $OfficeName[0]->name; ?></div>
            </div>
            <div class="pnel-body no-padding  col-sm-4">
               <div class='p10'><i data-feather='box' class='icon-16'></i> Division  </div>
               <div class='p10 pt0 b-b ml15'><?php echo $divisionsName[0]->name; ?></div>
            </div>
        </div>
    </div>
 <?php } ?>