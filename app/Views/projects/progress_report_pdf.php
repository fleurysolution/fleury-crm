<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Report - <?= esc($project['title']) ?></title>
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { border-bottom: 2px solid #0056b3; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
        .project-details { margin-bottom: 30px; }
        .project-details table { width: 100%; border-collapse: collapse; }
        .project-details th { text-align: left; color: #666; font-size: 10px; font-weight: normal; padding-bottom: 4px; border-bottom: 1px solid #ccc; }
        .project-details td { font-weight: bold; padding: 10px 0; font-size: 13px; }
        
        table.photo-grid { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 15px; margin-top: 20px; }
        td.photo-cell { width: 50%; vertical-align: top; }
        .photo-wrapper { padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #fff; text-align: center; }
        .photo-wrapper img { width: 100%; max-height: 350px; object-fit: contain; }
        .photo-info { text-align: left; padding-top: 8px; font-size: 11px; color: #555; }
        .photo-caption { font-weight: bold; margin-bottom: 4px; color: #333; }
        
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; padding-top: 10px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #eee; }
    </style>
</head>
<body>

<div class="header">
    <table width="100%">
        <tr>
            <td width="50%">
                <?php if (!empty($appSettings['company_logo'])): 
                    $logoPath = str_replace('\\', '/', FCPATH . $appSettings['company_logo']);
                ?>
                    <img src="file:///<?= $logoPath ?>" style="max-height: 60px; margin-bottom: 10px;">
                <?php endif; ?>
                <h1>Weekly Progress Report</h1>
                <p>Generated on <?= $date ?></p>
            </td>
            <td width="50%" style="text-align: right; vertical-align: top;">
                <h2 style="margin:0; color:#333;"><?= esc($project['title']) ?></h2>
                <div style="font-size: 11px; color: #666; margin-top: 5px;">
                    Client: <?= esc($project['client_name']) ?><br>
                    PM: <?= esc($project['pm_name']) ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="project-details">
    <table>
        <tr>
            <th width="25%">STATUS</th>
            <th width="25%">START DATE</th>
            <th width="25%">DUE DATE</th>
            <th width="25%">BUDGET</th>
        </tr>
        <tr>
            <td style="text-transform: capitalize; color: #0088cc;"><?= esc($project['status']) ?></td>
            <td><?= $project['start_date'] ? date('M j, Y', strtotime($project['start_date'])) : 'TBD' ?></td>
            <td><?= $project['end_date'] ? date('M j, Y', strtotime($project['end_date'])) : 'TBD' ?></td>
            <td><?= esc($project['currency']) ?> <?= number_format($project['budget'] ?? 0, 2) ?></td>
        </tr>
    </table>
</div>

<table class="photo-grid">
    <?php
    $count = 0;
    foreach ($photos as $photo):
        if ($count % 2 == 0) echo "<tr>";
    ?>
    <td class="photo-cell">
        <div class="photo-wrapper">
            <?php 
            $absPath = str_replace('\\', '/', FCPATH . $photo['photo_path']);
            if (file_exists(FCPATH . $photo['photo_path'])): 
            ?>
                <img src="file:///<?= $absPath ?>" alt="Progress Photo">
            <?php else: ?>
                <div style="height: 200px; line-height: 200px; color: #999; border: 1px dashed #ccc; background: #f9f9f9;">Image Missing</div>
            <?php endif; ?>
            <div class="photo-info">
                <div class="photo-caption"><?= esc($photo['title'] ?: $photo['caption']) ?></div>
                <?php if($photo['description']): ?>
                    <div style="margin-bottom: 4px; line-height: 1.4;"><?= nl2br(esc($photo['description'])) ?></div>
                <?php endif; ?>
                <span style="color: #999;">Logged: <?= date('M j, Y g:i A', strtotime($photo['created_at'])) ?></span>
            </div>
        </div>
    </td>
    <?php
        $count++;
        if ($count % 2 == 0) echo "</tr>";
    endforeach;
    
    // Close row if odd number of photos
    if ($count % 2 != 0) echo "<td class='photo-cell'></td></tr>";
    ?>
</table>

<div class="footer">
    Site Progress Photo Report &middot; <?= esc($project['title']) ?> &middot; System Generated Content
</div>

</body>
</html>
