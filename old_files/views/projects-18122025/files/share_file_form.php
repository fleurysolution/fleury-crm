<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->

<style>
  .select2-container .select2-selection--single {
    height: 38px;
    line-height: 38px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 12px;
}

</style>
<div id="page-content" class="page-wrapper clearfix">
				    <div class="col-md-6">
				            <div class="card bg-white">
							    <span class="p-4">Send Emails</span>

							    <div class="card-body pt0 rounded-bottom ps" id="proposals-widget-container">
							    	<form action="<?php echo base_url('file_manager/sendMailnow');?>" method="post" id="sendmailnow" class="general-form" role="form">
							    		<div class="form-group">
							    			<label> Email Sent to:</label>
							    			<input type="radio" name="user_type" value="list" onclick="showuser();" checked> Staff List
                        <input type="radio" name="user_type" value="list" onclick="showclients();" > Clients List
							    			<input type="radio" name="user_type" value="new_user" onclick="shownewuser();"> New User
							    		</div>
									<div class="form-group" id="users_list" class="show">
										<label>Select User</label>
										<select name="users[]" multiple class="form-control searchable-dropdown"  id="searchable-dropdown-users">
										<option value>Select </option>
										<?php if ($users) { ?>
											 <?php
										        foreach ($users as $user) { ?>
										 	<?php
										                        $subline = $user->job_title;
										                        if ($user->user_type === "client" && $user->company_name) {
										                            $subline = $user->company_name;
										                        }
										                        ?>
										<option value="<?php echo $user->id; ?>"><?php echo $user->first_name . " " . $user->last_name; ?><small class="text-off w200 d-block">  <?php echo $subline; ?></small></option>
										 <?php
										        }
										        ?>
										<?php } ?>
										</select>
									</div>

                  <div class="form-group" id="client_list" style="display: none;">
                    <label>Select Client</label>
                    <select name="users[]" multiple class="form-control searchable-dropdown"  id="searchable-dropdown">
                    <option value>Select </option>
                    <?php if ($clients) { ?>
                       <?php
                            foreach ($clients as $client) { ?>
                      <?php
                                            $subline = $client->job_title;
                                            if ($client->user_type === "client" && $client->company_name) {
                                                $subline = $client->company_name;
                                            }
                                            ?>
                    <option value="<?php echo $client->id; ?>"><?php echo $client->first_name . " " . $client->last_name; ?><small class="text-off w200 d-block"> - <?php echo $subline; ?></small></option>
                     <?php
                            }
                            ?>
                    <?php } ?>
                    </select>
                  </div>

							    		<?php $i=0; foreach($checkbox_file as $checkedfiles){ 
							    			?>
							    			 <input type="hidden" name="checkbox_file[]" checked value="<?php echo $checkbox_file[$i]; ?>">
							    			 <input type="hidden" name="project_ids_data[]" value="<?php echo $project_id[$i]; ?>">
											 <input type="hidden" name="client_ids_data[]" value="<?php echo $client_id[$i]; ?>">
							    		<?php $i++; } ?>
							    		<div id="new_users" style="display: none;">
									        <div class="form-group">
									        	<label> To </label>
									            <input type="text" name="emailsto" class="form-control" >
									            <input type="hidden" name="checkboxfiles" value="<?php echo implode(',',$checkbox_file); ?>">
											    <input type="hidden" name="project_ids" value="<?php echo implode(',',$project_id); ?>">
											    <input type="hidden" name="client_ids" value="<?php echo implode(',',$client_id); ?>">
									        </div>

										    <div class="form-group">
										        <label> CC </label>
										        <input type="text" name="emailscc" class="form-control" >
										    </div>

										    <div class="form-group">
										        <label> BCC </label>
										        <input type="text" name="emailsbcc" class="form-control" >
										    </div>
										</div>

										    <div class="form-group">
										        <label> Message </label>
										        <textarea class="form-control" name="messages" style="height: 300px;">
                              Hey, <br> 
Please Find the attachment for your reference.
<br>
Thank you <br> 
Fleury Solutions
										         </textarea>
										    </div>	
										    <div class="form-group">
										    	<input type="submit" name="send_mail" class="button btn btn-primary" value="Send Mail Now">
										    </div>						              
									</form>
							 	</div>

				       		</div>
					    </div>
        </div>
   
   <script type="text/javascript">
    $(document).ready(function() {
        $("#sendmailnow").appForm({
            onSuccess: function(result) {
                history.back();
            }
        });

    });
</script>

<script>
	function showuser(){
  document.getElementById('users_list').style.display ='block';
  document.getElementById('new_users').style.display ='none';
  document.getElementById('client_list').style.display ='none';  
}
function shownewuser(){
  document.getElementById('new_users').style.display = 'block';
  document.getElementById('users_list').style.display ='none';
  document.getElementById('client_list').style.display ='none';  
}
function showclients(){
  document.getElementById('new_users').style.display = 'none';
  document.getElementById('users_list').style.display ='none'; 
  document.getElementById('client_list').style.display ='block';  
}

</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.searchable-dropdown').select2({
            placeholder: "Select an option",
            allowClear: true
        });
    });
</script>
