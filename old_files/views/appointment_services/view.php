<div class="modal-body clearfix general-form">
    <div class="container-fluid">


        <div class="clearfix">
            <div class="col-md-12">
                <strong class="font-20"><?php echo $model_info->name; ?></strong>   
                <p>  <strong class="font-16"> Email: </strong>  <a href="mailto:<?php echo $model_info->email; ?>"> <?php echo $model_info->email; ?> </a></p>
                <p>  <strong class="font-16">Phone:  </strong> <a href="tel:<?php echo $model_info->phone; ?>"> <?php echo $model_info->phone; ?> </a></p>
                <p> <strong class="font-16"> Service Name : </strong> <?php echo $model_info->services_title; ?> </p>
                <p> <strong class="font-16"> Meeting Start Time : </strong> <?php echo $model_info->start_time; ?> </p>

                <p>  <strong class="font-16">Meeting End Time : </strong> <?php echo $model_info->end_time; ?> </p>

                <p> <strong class="font-16"> Payment Status :</strong>  <?php echo $model_info->payment_status; ?> </p>
            </div>
        </div>

        <div class="col-md-12 mb15">
          <strong class="font-16"> Meeting Link : </strong> 

          <?php if($model_info->meeting_link){ ?>
            <a href="<?php echo $model_info->meeting_link; ?>" target="_blank"><?php echo $model_info->meeting_link; ?></a> 
          <a href="<?php echo $model_info->meeting_link; ?>"  target="_blank" class="btn btn-default"> Click to join</a> 
      <?php }else{ echo "No Meeting Link Available"; } ?>
        </div>

        <div class="col-md-12 mb15">
          <strong class="font-16"> Appointment Status : </strong>  <span class="badge item-rate-badge font-16 strong"><?php echo $model_info->status; ?></span> 
        </div>

        <div class="col-md-12 mb15">
          <strong class="font-16"> Description: </strong>  <?php echo $model_info->description ? custom_nl2br(link_it(process_images_from_content($model_info->description))) : "-"; ?>
        </div>

        <div class="col-md-12 mb15">
         <strong class="font-16">Notes : </strong>         
            <?php echo $model_info->notes ? custom_nl2br(link_it(process_images_from_content($model_info->notes))) : "-"; ?>
        </div>


    </div>
</div>

<div class="modal-footer">
   

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>