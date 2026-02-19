<div class="container p-2">
    <h4>Assign Asset to Project: <?= esc($project[0]->title ?? $project[0]->name) ?></h4>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/projects/assign_asset') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="project_id" value="<?= esc($project[0]->id) ?>">

        <div class="row">
            <!-- Asset Select -->
            <div class="form-group col-sm-6">
                <label>Asset</label>
                <select name="asset_id" id="asset_id" class="form-select" required>
                    <option value="">-- Select asset --</option>
                    <?php foreach ($assets as $asset): ?>
                        <option value="<?= $asset->id ?>">
                            <?= esc($asset->asset_name) ?> (Available: <?= (int)$asset->quantity ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Billing Type -->
            <div class="form-group col-sm-4">
                <label>Billing Type</label>
                <select name="billing_type" id="billing_type_id" class="form-select" required>
                    <option value="">-- Select billing type --</option>
                    <option value="1">Hour</option>
                    <option value="2">Day</option>
                    <option value="3">Unit</option>
                    <option value="4">Storage</option>
                </select>
            </div>
            <div class="form-group col-sm-2">
                <label>Quantity</label>
                <input type="number" name="quantity" id="quantity" value="1" class="form-control" min="1">
            </div>
        </div>

        <div class="col-sm-12 row mt-3">

            <!-- PRICE INPUT (New Added) -->
            <div id="field_price" class="form-group col-md-3">
                <label>Rate (Price)</label>
                <input type="number" name="rate" id="rate" class="form-control" min="0" value="">
            </div>

            <!-- DATE RANGE (Day + Storage) -->
            <div id="field_date_range" class="row" style="display:none;">
                <div class="form-group col-md-3">
                    <label>From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" onchange="numberofDay();">
                </div>

                <div class="form-group col-md-3">
                    <label>To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" onchange="numberofDay();">
                </div>

                <div class="form-group col-md-3">
                    <label>No. of Days</label>
                    <input type="text" name="no_of_day" id="no_of_day" class="form-control" readonly>
                </div>
            </div>

            <!-- HOURLY / UNIT QTY FIELD -->
            <div id="field_hourly" class="form-group col-md-3" style="display:none;">
                <label>Hours</label>
                <input type="number" name="unit_quantity" id="unit_quantity" class="form-control" min="1" value="1">
            </div>

            <!-- UNIT FIELD -->
            <div id="field_unit" class="form-group col-md-3" style="display:none;">
                <label>Unit</label>
                <select name="unit_id" id="unit_id" class="form-select">
                    <option value="">-- Select unit --</option>
                    <?php foreach ($units as $u): ?>
                        <option value="<?= $u->id ?>"><?= esc($u->title) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- TOTAL AMOUNT -->
            <div class="form-group col-md-3">
                <label>Total Amount</label>
                <input type="text" name="price" id="assets_price" class="form-control" readonly value="0">
            </div>
        </div>

        <div class="form-group col-sm-12 mt-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="3"></textarea>
        </div>

        <button class="btn btn-success mt-3">Assign Asset</button>
        <button class="btn btn-danger mt-3 " data-bs-dismiss="modal">Back</button>
    </form>
</div>


<script>
// ----------------------------
// SHOW / HIDE FIELDS BASED ON BILLING TYPE
// ----------------------------
$("#billing_type_id").on("change", function () {

    let type = $("#billing_type_id option:selected").text().trim().toLowerCase();

    $("#field_date_range").hide();
    $("#field_hourly").hide();
    $("#field_unit").hide();

    if (type === "hour") {
        $("#field_hourly").show();
    }
    if (type === "day") {
        $("#field_date_range").show();
    }
    if (type === "unit") {
        $("#field_unit").show();
        $("#field_hourly").show();
    }
    if (type === "storage") {
        $("#field_date_range").show();
    }

    calculateTotal();
});


// ----------------------------
// CALCULATE TOTAL
// ----------------------------
function calculateTotal() {

    let unit_quantity = parseFloat($("#unit_quantity").val()) || 1;
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let days = parseInt($("#no_of_day").val()) || 1;
    let type = $("#billing_type_id option:selected").text().trim().toLowerCase();

    let total = 0;

    if (type === "hour") total = qty * unit_quantity * rate;
    else if (type === "day") total = qty * days * rate;
    else if (type === "unit") total = qty * unit_quantity * rate;
    else if (type === "storage") total = qty * days * rate;

    $("#assets_price").val(total);
}


// ----------------------------
// NUMBER OF DAYS
// ----------------------------
function numberofDay() {

    let from = $("#from_date").val();
    let to = $("#to_date").val();
    if (!from || !to) return;

    let d1 = new Date(from);
    let d2 = new Date(to);

    if (d2 < d1) {
        alert("To date cannot be less than From date");
        $("#to_date").val("");
        return;
    }

    let diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
    $("#no_of_day").val(diff);

    calculateTotal();
}


// Auto-recalculate on qty or price change
$("#quantity,#unit_quantity, #rate").on("input", function () {
    calculateTotal();
});
</script>
