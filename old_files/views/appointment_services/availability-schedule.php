<form action="<?= base_url('appointment_services/save_weekly') ?>"
      id="weeklyAvailabilityForm"
      method="post"
      class="p-4 border rounded shadow bg-light">

  <h4 class="mb-4">Weekly Availability Planner</h4>

  <div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" id="check_all_days">
    <label class="form-check-label fw-bold" for="check_all_days">Check/Uncheck All Days</label>
  </div>

  <?php
  $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

  foreach ($days as $day):
    $day_lower = strtolower($day);

    $active = isset($availabilityByDay[$day_lower]['active']) ? (int)$availabilityByDay[$day_lower]['active'] : 0;
    $start_time = $availabilityByDay[$day_lower]['start'] ?? '09:00';
    $end_time   = $availabilityByDay[$day_lower]['end'] ?? '18:00';

    $break_start = $availabilityByDay[$day_lower]['break_start'] ?? '';
    $break_end   = $availabilityByDay[$day_lower]['break_end'] ?? '';
  ?>
    <div class="border rounded p-3 mb-3">
      <div class="form-check mb-2">
        <input class="form-check-input day-toggle"
               type="checkbox"
               name="availability[<?= $day_lower ?>][active]"
               id="<?= $day_lower ?>_active"
               value="1"
               <?= $active ? 'checked' : ''; ?>>

        <label class="form-check-label fw-bold" for="<?= $day_lower ?>_active">
          <?= esc($day) ?>
        </label>
      </div>

      <div class="row g-3">
        <div class="col-md-3">
          <label for="<?= $day_lower ?>_start" class="form-label">Start Time</label>
          <input type="time"
                 class="form-control time-input"
                 name="availability[<?= $day_lower ?>][start]"
                 id="<?= $day_lower ?>_start"
                 value="<?= esc($start_time ?: '09:00') ?>">
        </div>

        <div class="col-md-3">
          <label for="<?= $day_lower ?>_end" class="form-label">End Time</label>
          <input type="time"
                 class="form-control time-input"
                 name="availability[<?= $day_lower ?>][end]"
                 id="<?= $day_lower ?>_end"
                 value="<?= esc($end_time ?: '18:00') ?>">
        </div>

        <div class="col-md-3">
          <label for="<?= $day_lower ?>_break_start" class="form-label">Break Start</label>
          <input type="time"
                 class="form-control break-input"
                 name="availability[<?= $day_lower ?>][break_start]"
                 id="<?= $day_lower ?>_break_start"
                 value="<?= esc($break_start) ?>">
          <small class="text-muted">Optional</small>
        </div>

        <div class="col-md-3">
          <label for="<?= $day_lower ?>_break_end" class="form-label">Break End</label>
          <input type="time"
                 class="form-control break-input"
                 name="availability[<?= $day_lower ?>][break_end]"
                 id="<?= $day_lower ?>_break_end"
                 value="<?= esc($break_end) ?>">
          <small class="text-muted">Optional</small>
        </div>

        <div class="col-md-3">
          <label for="<?= $day_lower ?>_duration" class="form-label">Working Minutes</label>
          <input type="number"
                 class="form-control duration-output"
                 name="availability[<?= $day_lower ?>][duration]"
                 id="<?= $day_lower ?>_duration"
                 readonly>
          <small class="text-muted">Auto-calculated</small>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <button type="submit" class="btn btn-primary">Save Weekly Availability</button>
</form>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

  function timeToMinutes(timeStr) {
    if (!timeStr) return null;
    const parts = timeStr.split(':');
    if (parts.length < 2) return null;
    const h = Number(parts[0]), m = Number(parts[1]);
    if (Number.isNaN(h) || Number.isNaN(m)) return null;
    return h * 60 + m;
  }

  function clamp(val, min, max) {
    return Math.min(max, Math.max(min, val));
  }

  function updateWorkingMinutes(day) {
    const startInput = document.getElementById(`${day}_start`);
    const endInput = document.getElementById(`${day}_end`);
    const breakStartInput = document.getElementById(`${day}_break_start`);
    const breakEndInput = document.getElementById(`${day}_break_end`);
    const durationInput = document.getElementById(`${day}_duration`);

    const start = timeToMinutes(startInput.value);
    const end = timeToMinutes(endInput.value);

    if (start === null || end === null || end <= start) {
      durationInput.value = '';
      return;
    }

    let total = end - start;

    const bStart = timeToMinutes(breakStartInput.value);
    const bEnd = timeToMinutes(breakEndInput.value);

    let breakMinutes = 0;

    // Break is optional but must be valid and inside start/end
    if (bStart !== null && bEnd !== null && bEnd > bStart) {
      if (bStart >= start && bEnd <= end) {
        breakMinutes = bEnd - bStart;
      } else {
        // If user sets outside range, ignore break for calculation
        breakMinutes = 0;
      }
    }

    const working = Math.max(0, total - breakMinutes);
    durationInput.value = working;
  }

  function toggleInputs(day) {
    const activeCheckbox = document.getElementById(`${day}_active`);
    const isActive = activeCheckbox.checked;

    const startInput = document.getElementById(`${day}_start`);
    const endInput = document.getElementById(`${day}_end`);
    const breakStartInput = document.getElementById(`${day}_break_start`);
    const breakEndInput = document.getElementById(`${day}_break_end`);
    const durationInput = document.getElementById(`${day}_duration`);

    startInput.disabled = !isActive;
    endInput.disabled = !isActive;
    breakStartInput.disabled = !isActive;
    breakEndInput.disabled = !isActive;

    if (!isActive) {
      durationInput.value = '';
      // Optional: clear break fields when disabled
      // breakStartInput.value = '';
      // breakEndInput.value = '';
    } else {
      updateWorkingMinutes(day);
    }
  }

  days.forEach(day => {
    const activeCheckbox = document.getElementById(`${day}_active`);
    const startInput = document.getElementById(`${day}_start`);
    const endInput = document.getElementById(`${day}_end`);
    const breakStartInput = document.getElementById(`${day}_break_start`);
    const breakEndInput = document.getElementById(`${day}_break_end`);

    toggleInputs(day);
    updateWorkingMinutes(day);

    activeCheckbox.addEventListener('change', () => toggleInputs(day));
    startInput.addEventListener('input', () => updateWorkingMinutes(day));
    endInput.addEventListener('input', () => updateWorkingMinutes(day));
    breakStartInput.addEventListener('input', () => updateWorkingMinutes(day));
    breakEndInput.addEventListener('input', () => updateWorkingMinutes(day));
  });

  const checkAll = document.getElementById('check_all_days');
  if (checkAll) {
    checkAll.addEventListener('change', function () {
      days.forEach(day => {
        const checkbox = document.getElementById(`${day}_active`);
        checkbox.checked = this.checked;
        toggleInputs(day);
      });
    });
  }

  // AJAX submit
  $('#weeklyAvailabilityForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: $(this).serialize(),
      success: function (response) {
        if (response && response.status === 'success') {
          alert('Availability saved successfully!');
        } else {
          alert((response && response.message) ? response.message : 'An error occurred, please try again.');
        }
      },
      error: function (xhr) {
        alert('Server error: ' + xhr.responseText);
      }
    });
  });
});
</script>
