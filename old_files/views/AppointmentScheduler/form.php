<!DOCTYPE html>
<html>
<head>
    <title>Free Consultation Appointment</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/js/select2/select2.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/js/select2/select2-bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/app.all.css">
</head>
<body>
    <div class="form-signin">
        <div class="card bg-white mb15 p-4">
            <!-- <div class="card-header text-center">
                <img src="https://bpms247.com/files/system/_file6802ea807a17f-site-logo.png">
            </div> -->
            <div class="card-header text-center">
               <h4>Schedule Free Consultation</h4>
            </div>
            
            <?php if(session()->getFlashdata('errors')): ?>
                <div style="color: red;">
                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo base_url(); ?>/appointment/submit">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="Name">Your Name:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="Email">Your Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="Phone">Your Phone:</label>
                        <input type="number" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="Date">Your Preferred Date:</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="Time">Your Preferred Time:</label>
                        <input type="time" name="time" class="form-control" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="Time">Duration:</label>
                        <select name="duration" class="form-select" required> 
                            <option value="15 Min">15 Min </option>
                            <option value="30 Min">30 Min </option>
                            <option value="45 Min">45 Min </option>
                            <option value="1 Hour">1 Hour </option>
                            <option value="More then 1 Hour">More then 1 hour </option>
                        </select>
                    </div>
                    <div class="col-sm-12">                
                        <label for="Time">Message:</label>
                        <textarea name="message" class="form-control"> </textarea> 
                    </div>
                    <div class="form-group col-sm-12 mt-2">

                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>