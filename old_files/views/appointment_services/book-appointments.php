<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" type="text/css" href="https://bpms247.com/assets/bootstrap/css/bootstrap.min.css?v=1.0" />
    <link rel="stylesheet" type="text/css" href="https://bpms247.com/assets/js/select2/select2.css?v=1.0" />
    <link rel="stylesheet" type="text/css" href="https://bpms247.com/assets/js/select2/select2-bootstrap.min.css?v=1.0" />
    <!-- <link rel="stylesheet" type="text/css" href="https://bpms247.com/assets/css/app.all.css?v=1.0" /> -->
    <link rel="stylesheet" type="text/css" href="https://bpms247.com/assets/css/custom-style.css?v=1.0" />

    <script type="text/javascript" src="https://bpms247.com/assets/js/app.all.js?v=1.0"></script>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .slot-btn.active { color: #fff !important; }
        .small-muted { font-size: 0.9rem; color: #6c757d; }
        .slot-btn { white-space: nowrap; }
        .loading-inline { display:inline-flex; align-items:center; gap:.5rem; }
        .loading-inline .spinner-border { width: 1rem; height: 1rem; }
    </style>
</head>

<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="text-center mb-3"><?php echo $title; ?></h2>

                    <!-- Inline message area (professional) -->
                    <div id="form-message" class="mb-3">
                        <?php
                        $status = $_GET['status'] ?? '';
                        $msg = $_GET['msg'] ?? '';

                        $messages = [
                            'invalid_service'       => 'The selected service is not available. Please select again.',
                            'invalid_time'          => 'Please choose a valid appointment time window.',
                            'past_time'             => 'You cannot book an appointment in the past.',
                            'no_staff_available'    => 'No team member is available for the selected time. Please choose another slot.',
                            'save_failed'           => 'We could not save your appointment. Please try again.',
                            'server_error'          => 'A server error occurred. Please try again later.',
                            'stripe_not_configured' => 'Payment is not available right now. Please try again later.',
                            'invalid_appointment'   => 'Invalid appointment reference.',
                            'not_found'             => 'Appointment not found.',
                        ];

                        if ($status === 'success') {
                            echo '<div class="alert alert-success mb-0">Your appointment has been scheduled successfully. Please check your email for further details.</div>';
                        } elseif ($status === 'fail') {
                            $text = $messages[$msg] ?? 'Your appointment could not be completed. Please try again.';
                            echo '<div class="alert alert-danger mb-0">' . esc($text) . '</div>';
                        }
                        ?>
                    </div>

                    <!-- Timezone clarity -->
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Timezone:</strong>
                            <span id="user-timezone-label">Detecting…</span>
                            <div class="small-muted">
                                Slots shown in your timezone. Appointment is stored in UTC to avoid international conflicts.
                            </div>
                        </div>
                        <span class="badge bg-primary" id="user-timezone-badge">—</span>
                    </div>

                    <?php echo form_open(get_uri("front/booking_save"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>

                    <!-- User timezone (recommended) -->
                    <input type="hidden" name="user_timezone" id="user_timezone" value="">

                    <!-- Selected slot values (UTC ISO 8601 recommended by your UI) -->
                    <input type="hidden" name="start_time" id="start_time" value="">
                    <input type="hidden" name="end_time" id="end_time" value="">

                    <div class="row g-3">

                        <!-- Category -->
                        <div class="col-12">
                            <label class="form-label"><?php echo app_lang('category'); ?></label>
                            <select name="category_id" id="category-select" class="form-select" required>
                                <option value=""> -- Select --</option>
                                <?php if (!empty($categories_list)) { foreach ($categories_list as $category) { ?>
                                    <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                <?php } } ?>
                            </select>
                        </div>

                        <!-- Service dropdown -->
                        <div class="col-12">
                            <label class="form-label"><?php echo app_lang('service'); ?></label>
                            <div id="dropdown">
                                <select class="form-select" disabled>
                                    <option value="">-- Select category first --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service details -->
                        <div class="col-12" id="dropdown_details"></div>

                        <!-- Date selector (Flatpickr) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">Select Date</label>
                            <input type="text" id="appointment_date" class="form-control" placeholder="Select a date" autocomplete="off" required>
                            <div class="small-muted mt-1" id="date-help">
                                Select a service to load available dates.
                            </div>
                        </div>

                        <!-- Slots -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">Available Slots</label>
                            <div id="slots-wrap" class="border rounded p-2" style="min-height: 42px;">
                                <div class="text-muted">Select category, service and date to load slots.</div>
                            </div>
                            <div class="small-muted mt-1">Break times are excluded automatically.</div>
                        </div>

                        <!-- Availability & breaks summary -->
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light" id="availability-summary" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Availability & Breaks (for selected date)</strong>
                                    <span class="small-muted" id="summary-note"></span>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="small-muted mb-1"><strong>Availability Windows</strong></div>
                                        <ul class="mb-0" id="availability-list"></ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small-muted mb-1"><strong>Break Times</strong></div>
                                        <ul class="mb-0" id="breaks-list"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer fields -->
                        <div class="col-12 col-md-6">
                            <label class="form-label"><?php echo app_lang('name'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "name",
                                "name" => "name",
                                "class" => "form-control validate-hidden",
                                "placeholder" => app_lang('name'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label"><?php echo app_lang('email'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "email",
                                "name" => "email",
                                "class" => "form-control validate-hidden",
                                "placeholder" => app_lang('email'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label"><?php echo app_lang('phone'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "phone",
                                "name" => "phone",
                                "class" => "form-control validate-hidden",
                                "placeholder" => app_lang('phone'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label"><?php echo app_lang('meeting_link'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "meeting_link",
                                "name" => "meeting_link",
                                "class" => "form-control",
                                "placeholder" => app_lang('meeting_link'),
                            ));
                            ?>
                            <div class="small-muted mt-1">Optional. If empty, staff can add later.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label"><?php echo app_lang('description'); ?></label>
                            <?php
                            echo form_textarea(array(
                                "id" => "description",
                                "name" => "description",
                                "class" => "form-control",
                                "placeholder" => app_lang('description'),
                                "rows" => 3
                            ));
                            ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label"><?php echo app_lang('notes'); ?></label>
                            <?php
                            echo form_textarea(array(
                                "id" => "notes",
                                "name" => "notes",
                                "class" => "form-control",
                                "placeholder" => app_lang('notes'),
                                "rows" => 3
                            ));
                            ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?>
                        </button>
                    </div>

                    <?php echo form_close(); ?>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
/**
 * Assumptions:
 * - `front/get_available_dates` returns JSON: { success:true, available_dates:[YYYY-MM-DD,...], timezone_label:"..." }
 * - `front/get_available_slots` returns JSON: { success:true, slots:[{start_utc,end_utc,display},...], availability:[...], breaks:[...], timezone_label:"..." }
 * - `front/getServicesByCategory` returns HTML containing <select id="service_id" ... onchange="getService(this.value)">
 * - `front/getServicesByService` returns HTML containing hidden/readonly duration, price, etc (optional for this UI)
 */

let fpDate = null;
let allowedDatesSet = new Set();

(function initTimezone() {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    document.getElementById('user_timezone').value = tz;
    document.getElementById('user-timezone-label').textContent = tz;
    document.getElementById('user-timezone-badge').textContent = tz;
})();

function showMessage(type, text) {
    const el = document.getElementById('form-message');
    if (!el) return;
    el.innerHTML = `<div class="alert alert-${type} mb-0">${escapeHtml(text)}</div>`;
}

function clearMessage() {
    const el = document.getElementById('form-message');
    if (!el) return;
    el.innerHTML = '';
}

function resetSlotsUI(messageText) {
    $('#slots-wrap').html(`<div class="text-muted">${escapeHtml(messageText)}</div>`);
    $('#start_time').val('');
    $('#end_time').val('');
    $('#availability-summary').hide();
    $('#availability-list').empty();
    $('#breaks-list').empty();
    $('#summary-note').text('');
}

function setDateHelp(text, isLoading) {
    const $help = $('#date-help');
    if (isLoading) {
        $help.html(`<span class="loading-inline"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span>${escapeHtml(text)}</span></span>`);
    } else {
        $help.text(text);
    }
}

function renderSummary(res) {
    const avail = res.availability || [];
    const breaks = res.breaks || [];
    const tzLabel = res.timezone_label || $('#user_timezone').val();

    if (!avail.length && !breaks.length) {
        $('#availability-summary').hide();
        return;
    }

    $('#availability-summary').show();
    $('#summary-note').text(`Shown in ${tzLabel}`);

    const $a = $('#availability-list').empty();
    if (avail.length) {
        avail.forEach(w => {
            const label = w.label ? `${w.label}: ` : (w.staff_name ? `${w.staff_name}: ` : '');
            $a.append(`<li>${escapeHtml(label)}${escapeHtml(w.start || '')} - ${escapeHtml(w.end || '')}</li>`);
        });
    } else {
        $a.append('<li class="text-muted">Not available</li>');
    }

    const $b = $('#breaks-list').empty();
    if (breaks.length) {
        breaks.forEach(b => {
            const title = b.title ? `${b.title}: ` : '';
            $b.append(`<li>${escapeHtml(title)}${escapeHtml(b.start || '')} - ${escapeHtml(b.end || '')}</li>`);
        });
    } else {
        $b.append('<li class="text-muted">No breaks</li>');
    }
}

function renderSlots(slots) {
    const $wrap = $('#slots-wrap');
    $wrap.empty();

    if (!slots || !slots.length) {
        resetSlotsUI('No slots available for this date. Please choose another date.');
        return;
    }

    const $container = $('<div class="d-flex flex-wrap gap-2"></div>');

    slots.forEach(slot => {
        const startUtc = slot.start_utc;
        const endUtc = slot.end_utc;
        const label = slot.display;

        const $btn = $(`
            <button type="button" class="btn btn-outline-primary btn-sm slot-btn"
                    data-start="${escapeAttr(startUtc)}"
                    data-end="${escapeAttr(endUtc)}">
                ${escapeHtml(label)}
            </button>
        `);

        $btn.on('click', function () {
            $('.slot-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).addClass('active btn-primary').removeClass('btn-outline-primary');

            $('#start_time').val($(this).data('start'));
            $('#end_time').val($(this).data('end'));
            clearMessage();
        });

        $container.append($btn);
    });

    $wrap.append($container);
}

function initOrUpdateFlatpickrEnabledDates(availableDates) {
    allowedDatesSet = new Set(availableDates || []);

    if (!fpDate) {
        fpDate = flatpickr("#appointment_date", {
            dateFormat: "Y-m-d",
            disableMobile: true,
            allowInput: false,
            minDate: "today",
            disable: [
                function(date) {
                    // date -> JS Date, convert to YYYY-MM-DD
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    const key = `${y}-${m}-${d}`;
                    return !allowedDatesSet.has(key);
                }
            ],
            onChange: function(selectedDates, dateStr) {
                if (!dateStr) return;
                loadSlotsIfReady();
            }
        });
    } else {
        // Reset date selection and update disable rule
        fpDate.clear();
        fpDate.set('disable', [
            function(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                const key = `${y}-${m}-${d}`;
                return !allowedDatesSet.has(key);
            }
        ]);
        fpDate.redraw();
    }
}

function getSelectedDate() {
    if (fpDate && fpDate.selectedDates && fpDate.selectedDates.length) {
        return fpDate.input.value; // already YYYY-MM-DD
    }
    return "";
}

function loadAvailableDates(serviceId) {
    clearMessage();

    if (!serviceId) {
        setDateHelp('Select a service to load available dates.', false);
        if (fpDate) fpDate.clear();
        initOrUpdateFlatpickrEnabledDates([]); // disable all
        resetSlotsUI('Select category, service and date to load slots.');
        return;
    }

    setDateHelp('Loading available dates…', true);
    resetSlotsUI('Select a date to load slots.');
    $('#availability-summary').hide();

    const tz = $('#user_timezone').val();
    const from = new Date();
    const fromStr = toYMD(from);

    const to = new Date();
    to.setDate(to.getDate() + 30);
    const toStr = toYMD(to);

    $.ajax({
        url: "<?php echo get_uri('front/get_available_dates'); ?>",
        type: "GET",
        dataType: "json",
        data: { service_id: serviceId, from: fromStr, to: toStr, timezone: tz },
        success: function(res) {
            if (!res || res.success === false) {
                const msg = (res && res.message) ? res.message : 'Unable to load available dates. Please try again.';
                setDateHelp('No available dates found.', false);
                initOrUpdateFlatpickrEnabledDates([]);
                showMessage('danger', msg);
                return;
            }

            const dates = res.available_dates || [];
            initOrUpdateFlatpickrEnabledDates(dates);

            if (!dates.length) {
                setDateHelp('No available dates found for this service.', false);
                resetSlotsUI('No slots available. Please select a different service.');
                return;
            }

            setDateHelp('Select a date to view available slots. Only available dates are enabled.', false);
        },
        error: function() {
            setDateHelp('Unable to load available dates right now.', false);
            initOrUpdateFlatpickrEnabledDates([]);
            showMessage('danger', 'Unable to load available dates right now. Please try again.');
        }
    });
}

function loadSlotsIfReady() {
    clearMessage();

    const serviceId = $('#service_id').val();
    const date = getSelectedDate();
    const tz = $('#user_timezone').val();

    if (!serviceId) {
        resetSlotsUI('Select a service to load slots.');
        return;
    }

    if (!date) {
        resetSlotsUI('Select a date to load slots.');
        return;
    }

    resetSlotsUI('Loading available slots…');

    $.ajax({
        url: "<?php echo get_uri('front/get_available_slots'); ?>",
        type: "GET",
        dataType: "json",
        data: { service_id: serviceId, date: date, timezone: tz },
        success: function(res) {
            if (!res || res.success === false) {
                const msg = (res && res.message) ? res.message : 'Unable to load availability. Please try again.';
                resetSlotsUI('No slots available.');
                showMessage('danger', msg);
                return;
            }

            renderSummary(res);
            renderSlots(res.slots || []);
        },
        error: function() {
            resetSlotsUI('No slots available.');
            showMessage('danger', 'Unable to load slots right now. Please try again.');
        }
    });
}

$('#category-select').on('change', function () {
    clearMessage();

    $('#dropdown_details').html('');
    resetSlotsUI('Select category, service and date to load slots.');
    setDateHelp('Select a service to load available dates.', false);

    // Reset datepicker (disable all until service is chosen)
    initOrUpdateFlatpickrEnabledDates([]);

    const categoryId = $(this).val();
    if (!categoryId) {
        $('#dropdown').html('<select class="form-select" disabled><option value="">-- Select category first --</option></select>');
        return;
    }

    $.ajax({
        url: "<?php echo get_uri('front/getServicesByCategory'); ?>",
        type: "GET",
        data: { category_id: categoryId },
        success: function (html) {
            $('#dropdown').html(html);
            // service dropdown is refreshed; user selects service next
        },
        error: function () {
            showMessage('danger', 'Unable to load services right now. Please try again.');
        }
    });
});

// Called by the generated services dropdown HTML: onchange="getService(this.value)"
function getService(serviceId) {
    clearMessage();

    $('#dropdown_details').html('');
    resetSlotsUI('Select a date to load slots.');
    // Reset datepicker enable list while loading new service dates
    initOrUpdateFlatpickrEnabledDates([]);
    setDateHelp('Loading available dates…', true);

    if (!serviceId) {
        setDateHelp('Select a service to load available dates.', false);
        showMessage('warning', 'Please select a service.');
        return;
    }

    // Load service details (price/duration/description)
    $.ajax({
        url: "<?php echo get_uri('front/getServicesByService'); ?>",
        type: "GET",
        data: { service_id: serviceId },
        success: function (html) {
            $('#dropdown_details').html(html);
            // After details load, load available dates and enable calendar accordingly
            loadAvailableDates(serviceId);
        },
        error: function () {
            setDateHelp('Unable to load service details.', false);
            showMessage('danger', 'Unable to load service details right now. Please try again.');
        }
    });
}

// Enforce slot selection before submit (professional)
$('#item-form').on('submit', function (e) {
    clearMessage();

    const categoryId = $('#category-select').val();
    const serviceId = $('#service_id').val();
    const date = getSelectedDate();
    const start = $('#start_time').val();
    const end = $('#end_time').val();

    if (!categoryId) {
        e.preventDefault();
        showMessage('warning', 'Please select a category.');
        return;
    }

    if (!serviceId) {
        e.preventDefault();
        showMessage('warning', 'Please select a service.');
        return;
    }

    if (!date) {
        e.preventDefault();
        showMessage('warning', 'Please select a date.');
        return;
    }

    if (!start || !end) {
        e.preventDefault();
        showMessage('warning', 'Please select an available time slot to continue.');
        return;
    }
});

/* Helpers */
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
function escapeAttr(str) { return escapeHtml(str).replaceAll('`', '&#096;'); }
function toYMD(dateObj) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dateObj.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

// Initialize flatpickr disabled until service selection
$(document).ready(function() {
    initOrUpdateFlatpickrEnabledDates([]);
});
</script>
</body>
</html>
