

<!-- Payment Success Content -->
<div class="container success-page">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
                    <div class="container my-5">
    <div class="card shadow">
      <div class="card-body">
        <!-- Payment Details -->
        <h2 class="mb-4">Thank you, Appointment Scheduled Successfully. </h2>
        

        <!-- Subscription Details -->
        <h2 class="mb-4">Appointment Details</h2>
        <ul class="list-group mb-4">
          <li class="list-group-item"><strong>Meeting Start time:</strong><?php echo $appointment_details->start_time; ?></li>
          <li class="list-group-item"><strong>Meeting Duration:</strong> <?php echo $appointment_details->duration; ?></li>
         
          <li class="list-group-item"><strong>Meeting Link :</strong> <?php echo $appointment_details->meeting_link;?></li>
        </ul>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-3">
         
        </div>
      </div>

      <div class="card-footer text-center">
        <p class="mb-0">A confirmation email has been sent to <strong><?php echo $appointment_details->email; ?></strong>.</p>
        <p>If you have any questions, <a href="<?php echo base_url('/#contact'); ?>" class="text-decoration-none">contact us</a>.</p>
      </div>
    </div>
  </div>
                
        </div>
    </div>
</div>