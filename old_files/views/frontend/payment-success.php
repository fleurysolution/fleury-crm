<?php include('includes/header.php'); ?>
<style>
    
    /* Ensure the card looks clean and is centered */
.card {
    margin-top: 50px;
}

/* Add some padding to the alert box */
.alert {
    padding: 20px;
    border-radius: 8px;
}

/* Customize the button */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 15px 30px;
    font-size: 18px;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

/* Add some responsive design for mobile devices */
@media (max-width: 768px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }

    .card {
        margin-top: 20px;
    }

    .btn-lg {
        width: 100%;
        font-size: 20px;
    }
}

</style>



<!-- Payment Success Content -->
<div class="container success-page">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
                    <div class="container my-5">
    <div class="card shadow">
      <div class="card-body">
        <!-- Payment Details -->
        <h2 class="mb-4">Payment Details</h2>
        <ul class="list-group mb-4">
          <li class="list-group-item"><strong>Subscription ID:</strong> <?php echo isset($subscription_id) ? $subscription_id : 'Waiting for confirmation'; ?></li>
          <li class="list-group-item"><strong>Transaction ID:</strong> <?php echo isset($invoiceData->payment_intent) ? $invoiceData->payment_intent : 'Waiting for confirmation'; ?></li>
          <li class="list-group-item"><strong>Amount Paid:</strong> <?php echo isset($amount_total) ? $amount_total.' '.strtoupper($currency) : 'Not available'; ?></li>
          <li class="list-group-item"><strong>Payment Date:</strong> <?php echo date('m-d-Y',$invoiceData->created); ?></li>
          <li class="list-group-item"><strong>Payment Status:</strong> <?php echo ucfirst($payment_status); ?></li>
        </ul>

        <!-- Subscription Details -->
        <h2 class="mb-4">Subscription Details</h2>
        <ul class="list-group mb-4">
          <li class="list-group-item"><strong>Plan:</strong><?php echo $invoiceData->lines->data[0]->description; ?></li>
          <li class="list-group-item"><strong>Start Date:</strong> <?php echo date('m-d-Y',$invoiceData->lines->data[0]->period->start); ?></li>
          <li class="list-group-item"><strong>Next Billing Date:</strong> <?php echo date('m-d-Y',$invoiceData->lines->data[0]->period->end);

           ?></li>
        </ul>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-3">
          <a href="<?php echo base_url('/dashboard'); ?>" class="btn btn-primary">Go to Dashboard</a>
          <a href="<?php echo $reciept_url; ?>" target="_blank" class="btn btn-secondary">Download Invoice</a>
        </div>
      </div>

      <div class="card-footer text-center">
        <p class="mb-0">A confirmation email has been sent to <strong><?php echo $invoiceData->customer_email; ?></strong>.</p>
        <p>If you have any questions, <a href="<?php echo base_url('/#contact'); ?>" class="text-decoration-none">contact us</a>.</p>
      </div>
    </div>
  </div>
                    <!-- <div class="text-center">
                        <a href="<?php echo $reciept_url; ?>"  class="btn btn-default btn-lg mr-5"  target="_blank" 
   >    Download Invoice & Reciept
</a>
                        <a href="<?php echo base_url(); ?>" class="btn btn-primary btn-lg">Go to Homepage</a>
                    </div>
                </div> 
            </div>-->
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
