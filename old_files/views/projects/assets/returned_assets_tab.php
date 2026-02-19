
<div class="container">
    <h4>Project Returned Assets </h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- <a href="<?= base_url('projects/assign_asset_form/' . $project_id); ?>" class="btn btn-primary mb-3">
        Assign Asset
    </a> -->
    <!-- <a href="<?= base_url("projects/assign_asset_form/$project_id"); ?>" class="btn btn-primary">   Assign Asset--</a> -->
      <?php 
 if (get_array_value($permissions, "can_assign_asset_on_projects") == "1" || $is_admin == "1") {
                    
    echo modal_anchor(get_uri("projects/assign_asset_form/".$project_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('assign_asset'), array("class" => "btn btn-default", "title" => app_lang('assign_asset'))); 

                    }
    ?>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Asset</th>
                <th>Series</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Cost($)</th>
                <th>Returned Date</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($assignments)): $i = 1; foreach ($assignments as $a): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= esc($a["asset_name"]); ?></td>
                <td><?= esc($a["series"]); ?></td>
                <td><?= esc($a["quantity"]); ?></td>
                <td><?= esc($a["unit_title"]); ?></td>
                <td><?= esc($a["cost"]); ?></td>
                <td><?php $return_date=$a["return_date"]; echo date("d M Y H:i", strtotime($return_date)); ?></td>
                <td><?= esc(ucfirst($a["status"])); ?></td>
                <td><?= esc($a["remarks"]); ?></td>
                <td>
                    <?php if ($a["status"] === "assigned"): ?>
                        <a href="<?php echo base_url("/projects/return_asset/" . $a["id"]) ?>"
                           class="btn btn-sm btn-danger">Return</a>
                    <?php else: ?>
                        <span class="badge badge-secondary">Returned</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="9">No assets assigned to this project.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
