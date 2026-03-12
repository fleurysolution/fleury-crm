<?php
// app/Views/projects/tabs/gantt_inline.php
// Included inside show.php — has access to $project, $phases
?>
<?php
// Download Frappe Gantt locally if not present
$ganttCssPath = FCPATH . 'assets/vendor/frappe-gantt/frappe-gantt.min.css';
$ganttJsPath  = FCPATH . 'assets/vendor/frappe-gantt/frappe-gantt.min.js';
$ganttCssUrl  = base_url('assets/vendor/frappe-gantt/frappe-gantt.min.css');
$ganttJsUrl   = base_url('assets/vendor/frappe-gantt/frappe-gantt.min.js');
$hasFrappe    = file_exists($ganttCssPath) && file_exists($ganttJsPath);
?>

<?php if (!$hasFrappe): ?>
<!-- Frappe Gantt not installed locally: load from CDN as fallback -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<?php else: ?>
<link rel="stylesheet" href="<?= $ganttCssUrl ?>">
<script src="<?= $ganttJsUrl ?>"></script>
<?php endif; ?>

<style>
.gantt-toolbar { display:flex; align-items:center; gap:.5rem; margin-bottom:.75rem; }
.gantt-container { overflow-x:auto; background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.06); padding:1rem; min-height:400px; }
.gantt .bar-label { font-size:12px; }
.gantt .bar.task-done        .bar-progress { fill: #198754; }
.gantt .bar.task-in_progress .bar-progress { fill: #0d6efd; }
.gantt .bar.task-blocked     .bar-progress { fill: #dc3545; }
.gantt .bar.task-review      .bar-progress { fill: #ffc107; }
.gantt .bar.task-todo        .bar-progress { fill: #6c757d; }
</style>

<!-- Toolbar -->
<div class="gantt-toolbar">
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-secondary" onclick="ganttChart.change_view_mode('Day')">Day</button>
        <button type="button" class="btn btn-outline-primary active" onclick="ganttChart.change_view_mode('Week')">Week</button>
        <button type="button" class="btn btn-outline-secondary" onclick="ganttChart.change_view_mode('Month')">Month</button>
    </div>
    <div class="btn-group btn-group-sm ms-2">
        <button class="btn btn-outline-danger" onclick="recalculateCPM()">
            <i class="fa-solid fa-calculator me-1"></i>Recalculate CPM
        </button>
        <button class="btn btn-outline-info" onclick="document.getElementById('xerUpload').click()">
            <i class="fa-solid fa-file-import me-1"></i>Import XER
        </button>
        <input type="file" id="xerUpload" style="display:none" onchange="uploadXer(this)" accept=".xer">
    </div>
    <div class="ms-auto d-flex gap-2 small text-muted align-items-center">
        <span class="d-inline-block" style="width:10px;height:10px;border-radius:2px;background:#dc3545;"></span> Critical Path
        <span class="d-inline-block" style="width:10px;height:10px;border-radius:2px;background:#0d6efd;"></span> In Progress
        <span class="d-inline-block" style="width:10px;height:10px;border-radius:2px;background:#198754;"></span> Done
    </div>
    <button class="btn btn-sm btn-primary ms-2" onclick="openNewTaskModal()">
        <i class="fa-solid fa-plus me-1"></i>Add Task
    </button>
</div>

<div class="gantt-container">
    <div id="ganttChart"></div>
    <div id="ganttLoadingMsg" class="text-center py-5 text-muted">
        <div class="spinner-border spinner-border-sm me-2"></div> Loading Gantt…
    </div>
</div>

<!-- Task quick-edit popup (shown on Gantt bar click) -->
<div class="modal fade" id="ganttTaskModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h6 class="modal-title fw-semibold" id="ganttTaskName">Task</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" id="ganttTaskBody"></div>
    <div class="modal-footer border-0">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-sm btn-primary" onclick="saveGanttTask()">Save Dates</button>
    </div>
</div>
</div>
</div>

<script>
let ganttChart;
let editingTaskId;

document.addEventListener('DOMContentLoaded', async () => {
    loadGanttData();
});

async function loadGanttData() {
    document.getElementById('ganttLoadingMsg').style.display = 'block';
    const res  = await fetch(`/staging/public/projects/<?= $project['id'] ?>/gantt/data`, {
        headers: {'X-Requested-With':'XMLHttpRequest'}
    });
    const data = await res.json();
    document.getElementById('ganttLoadingMsg').style.display = 'none';

    if (!data.tasks || data.tasks.length === 0) {
        document.getElementById('ganttChart').innerHTML = '<p class="text-muted text-center py-5">No tasks with dates yet. Add start/end dates or import an XER file.</p>';
        return;
    }

    const tasks = data.tasks.map(t => {
        if (t.is_critical == 1) t.custom_class = 'gantt-critical-bar';
        return t;
    });

    ganttChart = new Gantt('#ganttChart', tasks, {
        view_mode: 'Week',
        date_format: 'YYYY-MM-DD',
        bar_height: 28,
        padding: 14,
        on_click: function(task) { openGanttTaskEdit(task); },
        on_date_change: function(task, start, end) {
            autoSaveTaskDates(task.id, formatDate(start), formatDate(end));
        },
        on_progress_change: function(task, progress) {
            autoSaveProgress(task.id, progress);
        },
        popup_trigger: 'none',
    });
}

function uploadXer(input) {
    if (!input.files.length) return;
    const formData = new FormData();
    formData.append('xer_file', input.files[0]);
    formData.append(CSRF_NAME, CSRF_TOKEN);

    fetch(`/staging/public/projects/<?= $project['id'] ?>/gantt/import`, {
        method: 'POST',
        body: formData
    }).then(r=>r.json()).then(res => {
        if (res.success) {
            alert('Import successful: ' + res.count + ' tasks imported.');
            loadGanttData();
        } else {
            alert('Import failed: ' + (res.message || 'Unknown error'));
        }
    });
}

function recalculateCPM() {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`/staging/public/projects/<?= $project['id'] ?>/gantt/recalculate`, {
        method: 'POST',
        body: fd
    }).then(() => loadGanttData());
}

function formatDate(d) {
    const dt = new Date(d);
    return dt.toISOString().split('T')[0];
}

function autoSaveTaskDates(taskId, start, end) {
    if (isNaN(parseInt(taskId))) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('start_date', start);
    fd.append('end_date',   end);
    fetch(`/staging/public/tasks/${taskId}/gantt-update`, { method:'POST', body: fd });
}

function autoSaveProgress(taskId, progress) {
    if (isNaN(parseInt(taskId))) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('progress', Math.round(progress));
    fetch(`/staging/public/tasks/${taskId}/gantt-update`, { method:'POST', body: fd });
}

function openGanttTaskEdit(task) {
    if (isNaN(parseInt(task.id))) return; // phase group row
    editingTaskId = task.id;
    document.getElementById('ganttTaskName').textContent = task.name;
    document.getElementById('ganttTaskBody').innerHTML = `
        <div class="row g-2">
            <div class="col-6">
                <label class="form-label small fw-semibold">Start Date</label>
                <input type="date" id="editStart" class="form-control form-control-sm" value="${task.start}">
            </div>
            <div class="col-6">
                <label class="form-label small fw-semibold">End Date</label>
                <input type="date" id="editEnd" class="form-control form-control-sm" value="${task.end}">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Progress: <span id="progVal">${task.progress}%</span></label>
                <input type="range" class="form-range" id="editProgress" min="0" max="100" value="${task.progress}"
                    oninput="document.getElementById('progVal').textContent=this.value+'%'">
            </div>
            <div class="col-12 small text-muted">
                Assignee: ${task.assignee || '—'} | Status: ${task.status}
                <a href="#" onclick="openTask(${task.id});return false;" class="ms-2">Open full detail →</a>
            </div>
        </div>`;
    new bootstrap.Modal(document.getElementById('ganttTaskModal')).show();
}

function saveGanttTask() {
    const start = document.getElementById('editStart').value;
    const end   = document.getElementById('editEnd').value;
    const prog  = document.getElementById('editProgress').value;
    autoSaveTaskDates(editingTaskId, start, end);
    autoSaveProgress(editingTaskId, prog);
    bootstrap.Modal.getInstance(document.getElementById('ganttTaskModal')).hide();
}
</script>
