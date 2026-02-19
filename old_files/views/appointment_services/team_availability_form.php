<?php
/**
 * View: appointment_services/team_availability_form.php
 *
 * Expects:
 *  - $staff_id (int)
 *  - $availability (array of rows) from pcm_weekly_availability
 *  - $breaks (array of rows) from pcm_weekly_breaks
 */

$days = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];

// Map availability by day for easy lookup
$availByDay = [];
if (!empty($availability)) {
    foreach ($availability as $row) {
        $availByDay[$row->day_of_week] = $row;
    }
}

// Group breaks by day
$breaksByDay = [];
if (!empty($breaks)) {
    foreach ($breaks as $b) {
        $breaksByDay[$b->day_of_week][] = $b;
    }
}
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Team Availability & Break Scheduler</h4>
        <div class="text-muted mt-1">
            Configure weekly availability and breaks. These rules will be used by the appointment scheduler (slots + round-robin).
        </div>
    </div>

    <div class="card-body">

        <div id="avail-message" class="mb-3"></div>

        <div class="alert alert-info">
            <strong>Tip:</strong> Keep availability windows realistic (e.g., 10:00–18:00). Add breaks for lunch or meetings.
            Breaks must fall inside the day’s availability window.
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                <tr>
                    <th style="width: 110px;">Day</th>
                    <th style="width: 120px;">Available?</th>
                    <th style="width: 140px;">Start</th>
                    <th style="width: 140px;">End</th>
                    <th>Breaks</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($days as $day): ?>
                    <?php
                    $win = $availByDay[$day] ?? null;
                    $is_available = $win ? (int)$win->is_available : 0;
                    $start_time = $win->start_time ?? "10:00:00";
                    $end_time   = $win->end_time ?? "18:00:00";
                    ?>
                    <tr data-day="<?php echo esc($day); ?>">
                        <td><strong><?php echo esc($day); ?></strong></td>

                        <td>
                            <select class="form-select form-select-sm avail-is-available">
                                <option value="1" <?php echo $is_available === 1 ? "selected" : ""; ?>>Yes</option>
                                <option value="0" <?php echo $is_available === 0 ? "selected" : ""; ?>>No</option>
                            </select>
                        </td>

                        <td>
                            <input type="time" class="form-control form-control-sm avail-start"
                                   value="<?php echo esc(substr($start_time, 0, 5)); ?>">
                        </td>

                        <td>
                            <input type="time" class="form-control form-control-sm avail-end"
                                   value="<?php echo esc(substr($end_time, 0, 5)); ?>">
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-2">

                                <!-- Existing breaks list -->
                                <div class="breaks-list">
                                    <?php if (!empty($breaksByDay[$day])): ?>
                                        <?php foreach ($breaksByDay[$day] as $b): ?>
                                            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-1 break-row"
                                                 data-break-id="<?php echo (int)$b->id; ?>">
                                                <div>
                                                    <div class="fw-semibold">
                                                        <?php echo esc($b->title ?: "Break"); ?>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <?php echo esc(substr($b->start_time, 0, 5)); ?>
                                                        –
                                                        <?php echo esc(substr($b->end_time, 0, 5)); ?>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-sm break-delete">
                                                    Delete
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small no-breaks">No breaks</div>
                                    <?php endif; ?>
                                </div>

                                <!-- Add break form -->
                                <div class="border rounded p-2 bg-light">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <input type="time" class="form-control form-control-sm break-start" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" class="form-control form-control-sm break-end" value="">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm break-title"
                                                   placeholder="Title (optional)" value="">
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button type="button" class="btn btn-primary btn-sm break-add">Add</button>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        Breaks are excluded from slots automatically.
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <button type="button" id="save-all-availability" class="btn btn-success">
                Save All Availability
            </button>
        </div>

    </div>
</div>

<script>
(function(){
    const staffId = <?php echo (int)$staff_id; ?>;

    function showMsg(type, text) {
        const el = document.getElementById("avail-message");
        if (!el) return;
        el.innerHTML = `<div class="alert alert-${type} mb-0">${escapeHtml(text)}</div>`;
    }

    function clearMsg() {
        const el = document.getElementById("avail-message");
        if (!el) return;
        el.innerHTML = "";
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return "";
        return String(str)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function post(url, data) {
        return $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: data
        });
    }

    // Save one day availability
    function saveDay($tr) {
        const day = $tr.data("day");
        const isAvailable = parseInt($tr.find(".avail-is-available").val(), 10) || 0;
        const start = ($tr.find(".avail-start").val() || "").trim();
        const end   = ($tr.find(".avail-end").val() || "").trim();

        return post("<?php echo get_uri('team_members/save_weekly_availability'); ?>", {
            staff_id: staffId,
            day_of_week: day,
            is_available: isAvailable,
            start_time: start,
            end_time: end
        });
    }

    // Save all availability
    $("#save-all-availability").on("click", function(){
        clearMsg();

        const $rows = $("tr[data-day]");
        if (!$rows.length) return;

        const btn = this;
        btn.disabled = true;
        btn.textContent = "Saving…";

        const tasks = [];
        $rows.each(function(){
            tasks.push(saveDay($(this)));
        });

        Promise.all(tasks.map(p => p.then(r => r).catch(e => ({success:false, message:"Save failed"}))))
            .then(results => {
                const failed = results.find(r => !r || r.success === false);
                if (failed) {
                    showMsg("danger", failed.message || "Unable to save availability.");
                } else {
                    showMsg("success", "Availability saved successfully.");
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = "Save All Availability";
            });
    });

    // Add break
    $(document).on("click", ".break-add", function(){
        clearMsg();
        const $tr = $(this).closest("tr[data-day]");
        const day = $tr.data("day");

        const start = ($tr.find(".break-start").val() || "").trim();
        const end   = ($tr.find(".break-end").val() || "").trim();
        const title = ($tr.find(".break-title").val() || "").trim();

        if (!start || !end) {
            showMsg("warning", "Please select break start and end time.");
            return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Adding…");

        post("<?php echo get_uri('team_members/save_weekly_break'); ?>", {
            staff_id: staffId,
            day_of_week: day,
            start_time: start,
            end_time: end,
            title: title
        }).done(function(res){
            if (!res || res.success === false) {
                showMsg("danger", (res && res.message) ? res.message : "Unable to add break.");
                return;
            }

            // Update UI by reloading the page section (simple + reliable).
            // If you want partial DOM update only, we can do it too.
            location.reload();
        }).fail(function(){
            showMsg("danger", "Unable to add break right now. Please try again.");
        }).always(function(){
            $btn.prop("disabled", false).text("Add");
        });
    });

    // Delete break
    $(document).on("click", ".break-delete", function(){
        clearMsg();

        const $row = $(this).closest(".break-row");
        const id = parseInt($row.data("break-id"), 10);

        if (!id) return;

        const $btn = $(this);
        $btn.prop("disabled", true).text("Deleting…");

        post("<?php echo get_uri('team_members/delete_weekly_break'); ?>", { id: id })
            .done(function(res){
                if (!res || res.success === false) {
                    showMsg("danger", (res && res.message) ? res.message : "Unable to delete break.");
                    return;
                }
                $row.remove();
                showMsg("success", "Break deleted.");
            })
            .fail(function(){
                showMsg("danger", "Unable to delete break right now. Please try again.");
            })
            .always(function(){
                $btn.prop("disabled", false).text("Delete");
            });
    });

})();
</script>
