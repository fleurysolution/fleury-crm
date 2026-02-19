<div id="page-content" class="clearfix">
    <?php
    load_css(array(
        "assets/css/invoice.css",
    ));
    ?>

    <div class="proposal-preview-container bg-white mt15">
        <div class="row">
            <div class="col-md-12 position-relative">
                <div class="ribbon"><?php echo $proposal_status_label; ?></div>
            </div>
        </div>

        <?php echo $proposal_preview; ?>
    </div>

    <!-- Print button -->
    <div class="text-center mt-3">
        <button id="print-proposal-btn" class="btn btn-info text-white">
            <i data-feather='printer' class='icon-16'></i> Print Proposal
        </button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Add click event listener to the print button
        $("#print-proposal-btn").click(function () {
            // Add print media query to make sure only printable content is displayed
            $("body").addClass("dt-print-view");

            // Delay the print action to ensure all content is rendered
            setTimeout(function () {
                // Trigger the print dialog
                window.print();
                
                // Remove print media query class after printing is done
                $("body").removeClass("dt-print-view");
            }, 1000); // Adjust delay as needed (in milliseconds)
        });
    });
</script>