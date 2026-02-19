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
                                <h2 class="form-signin-heading">Book Your Appointment Today</h2>
                                <!-- <p><?php //echo $signup_message; ?> Enter the Details below. These credentials will be used to login your CRM and it will be used to login bpms247.com.</p>
                                <p><b> Note: All <span class="danger">*</span> fields are required </b> </p> -->
                            </div>
                            <div class="card-body p30 rounded-bottom">
                               

                                <div class="row" style="width:100%; height:100%; border:0;">
                                    <iframe 
                                        src="https://bpms247.com/front/book_appointment"
                                         style="width:100%; height:100vh; border:0;">
                                    </iframe>
                                    

                                   
                                </div>
                                     
                                    
                                
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
   
  
    <?php include('includes/footer.php'); ?>