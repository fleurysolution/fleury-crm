<?php 
$this->Project_files_model = model('App\Models\Project_files_model'); 
$this->Users_model = model('App\Models\Users_model');
$this->Settings_model = model('App\Models\Settings_model');
$this->Projects_model = model('App\Models\Projects_model'); 
$this->Custom_fields_model = model('App\Models\Custom_fields_model');  
?> 

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Project Report</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 13px;
      color: #333;
      margin: 30px;
      position: relative;
    }

    /* Header Section */
    .report-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .report-header img {
      height: 60px;
    }

    .report-info {
      text-align: right;
      font-size: 12px;
      line-height: 1.4;
    }

    /* 2 Column Layout */
    .cards {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .card {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      box-sizing: border-box;
      page-break-inside: avoid;
      text-align: left;
    }

    .file-preview {
      width: 100%;
      height: 220px;
      object-fit: contain;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .file-icon {
      font-size: 60px;
      text-align: center;
      color: #666;
      margin: 40px 0 10px;
    }

    .file-label {
      text-align: center;
      font-size: 12px;
      color: #444;
      word-break: break-all;
    }

    .description {
      font-weight: bold;
      margin-top: 5px;
      margin-bottom: 8px;
    }

    .details {
      font-size: 12px;
      line-height: 1.6;
    }

    .details span {
      display: block;
    }

    /* Footer Page Number (Visible only in print) */
    @media print {
      @page {
        margin: 15mm;
        @bottom-center {
          content: "Page " counter(page) " of " counter(pages);
          font-size: 12px;
          color: #555;
        }
      }

      body {
        margin: 10mm;
      }

      .card {
        break-inside: avoid;
      }

      body::after {
        counter-increment: page;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 12px;
        color: #555;
        content: "Page " counter(page);
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="report-header">
    <div>
      <img src="<?php echo get_logo_url(); ?>" alt="Logo">
      <h2 align="center"><?php echo get_setting('app_title'); ?></h2>
    </div>
    <div class="report-info">
      <p><strong>Printed on:</strong> <?php echo date('m-d-Y h:i A'); ?></p>
      <p><strong>Job #:</strong> <?php echo $project_id[0]; ?></p>
      <p>
        <?php 
          $project_data = $this->Projects_model->get_one($project_id[0]); 
          $Custom_fields = $this->Custom_fields_model->get_combined_details("projects", $project_id[0])->getResult(); 
          echo !empty($Custom_fields[0]->value) ? $Custom_fields[0]->value : '';
        ?> 
      </p>
    </div>
  </div>

  <!-- Cards Section -->
  <div class="cards">
    <?php 
    $i = 0; 
    foreach ($checkbox_file as $checkedfiles) {  
        $project_info = $this->Project_files_model->get_one($checkbox_file[$i]); 
        $Users_info = $this->Users_model->get_one($project_info->uploaded_by); 

        $file_path = get_uri('files/project_files/'.$project_info->project_id.'/' . $project_info->file_name);
        $extension = strtolower(pathinfo($project_info->file_name, PATHINFO_EXTENSION));
        $image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    ?>
      <div class="card">
        <?php if (in_array($extension, $image_types)) { ?>
            <img src="<?php echo $file_path; ?>" alt="Image" class="file-preview">
        <?php } else { ?>
            <div class="file-icon">
              <?php 
                $icon = get_file_icon($extension);
                echo "<i data-feather='{$icon}'></i>";
              ?>
            </div>
            <div class="file-label"><?php echo strtoupper($extension); ?> File</div>
        <?php } ?>

        <div class="description"><?php echo !empty($project_info->description) ? $project_info->description : 'No description'; ?></div>
        <div class="details">
          <span><strong>Taken Date:</strong> <?php echo $project_info->created_at; ?></span>
          <span><strong>Uploaded By:</strong> <?php echo $Users_info->first_name .' '. $Users_info->last_name; ?></span>
          <span><strong>File Name:</strong> <?php echo $project_info->file_name; ?></span>
          <span><strong>Upload Date:</strong> <?php echo date('m-d-Y h:i:s'); ?></span>
          <span><strong>Album:</strong> <?php echo $project_info->service_type; ?></span>
        </div>
      </div>
    <?php $i++; } ?>
  </div>

  <script>
    feather.replace();
    window.print();
  </script>

</body>
</html>
