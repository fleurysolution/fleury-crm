<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php
$statusColors  = ['draft'=>'secondary','submitted'=>'warning','approved'=>'success','rejected'=>'danger'];
$statusLabels  = ['draft'=>'Draft','submitted'=>'Submitted','approved'=>'Approved','rejected'=>'Rejected'];
$weekEnd       = date('d M Y', strtotime($ts['week_start'] . ' +6 days'));
$weekStartFmt  = date('d M Y', strtotime($ts['week_start']));
$canEdit       = $ts['status'] === 'draft' && $ts['user_id'] === $currentUser['id'];
$canApprove    = in_array('Finance', $currentUserRoles ?? []) || in_array('Admin', $currentUserRoles ?? []) || in_array('PM', $currentUserRoles ?? []);
?>

<div class="d-flex align-items-start justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="fa-solid fa-clock me-2 text-primary"></i>
            Timesheet: <?= $weekStartFmt ?> – <?= $weekEnd ?>
        </h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('timesheets') ?>" class="text-decoration-none">Timesheets</a></li>
            <li class="breadcrumb-item active"><?= $weekStartFmt ?></li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-<?= $statusColors[$ts['status']] ?>-subtle text-<?= $statusColors[$ts['status']] ?> px-3 py-2 fw-semibold">
            <?= $statusLabels[$ts['status']] ?>
        </span>
        <?php if ($ts['status'] === 'draft'): ?>
        <button class="btn btn-sm btn-success" onclick="saveAndSubmit()">
            <i class="fa-solid fa-paper-plane me-1"></i>Submit for Approval
        </button>
        <?php elseif ($ts['status'] === 'submitted' && $canApprove): ?>
        <form method="post" action="<?= site_url("timesheets/{$ts['id']}/approve") ?>" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-success">
                <i class="fa-solid fa-check me-1"></i>Approve
            </button>
        </form>
        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
            Reject
        </button>
        <?php endif; ?>
        <a href="<?= site_url("timesheets/{$ts['id']}/export") ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-download me-1"></i>CSV
        </a>
    </div>
</div>

<?php if ($ts['status'] === 'rejected' && $ts['rejected_reason']): ?>
<div class="alert alert-danger mb-3">
    <strong>Rejected:</strong> <?= esc($ts['rejected_reason']) ?>
</div>
<?php endif; ?>

<!-- Total hours card -->
<div class="d-flex gap-3 mb-3">
    <div class="card border-0 bg-primary-subtle text-center px-4 py-2" style="border-radius:10px;">
        <div class="text-muted small">Total Hours</div>
        <div class="fw-bold fs-4 text-primary" id="totalHoursDisplay"><?= number_format($totalHours,1) ?></div>
    </div>
    <?php
    $byProject = [];
    foreach ($entries as $e) {
        if (!$e['project_id']) continue;
        $pid = $e['project_id'];
        $byProject[$pid] = ($byProject[$pid] ?? 0) + (float)$e['hours'];
        if (!isset($byProject['name_'.$pid])) $byProject['name_'.$pid] = $e['project_title'] ?? 'Project #'.$pid;
    }
    foreach ($byProject as $k => $v):
        if (str_starts_with((string)$k,'name_')) continue; ?>
    <div class="card border-0 bg-light text-center px-3 py-2" style="border-radius:10px;">
        <div class="text-muted" style="font-size:.7rem;"><?= esc($byProject['name_'.$k] ?? '') ?></div>
        <div class="fw-bold"><?= number_format($v,1) ?>h</div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Weekly Grid -->
<div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
<?php
// Group entries by project_id
$grouped = [];
foreach ($entries as $e) {
    $grouped[$e['project_id'] ?? 0][] = $e;
}
// Day labels
$dayLabels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
?>
<div class="table-responsive">
<table class="table align-middle mb-0" id="tsGrid">
    <thead class="table-light">
        <tr>
            <th style="min-width:160px;">Project / Task</th>
            <?php foreach ($days as $i => $day): ?>
            <th class="text-center" style="min-width:95px;">
                <div class="fw-semibold"><?= $dayLabels[$i] ?></div>
                <div class="text-muted" style="font-size:.72rem;"><?= date('d M', strtotime($day)) ?></div>
            </th>
            <?php endforeach; ?>
            <th class="text-center" style="min-width:60px;">Total</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($grouped as $projId => $projEntries):
        $projTitle = $projEntries[0]['project_title'] ?? 'No Project';
        $projTotal = array_sum(array_column($projEntries, 'hours'));
    ?>
    <tr class="table-secondary">
        <td colspan="9" class="fw-semibold small py-1 ps-3">
            <i class="fa-solid fa-layer-group me-1 text-primary"></i><?= esc($projTitle) ?>
        </td>
    </tr>
    <?php
    // Group project entries by entry_date
    $entByDate = [];
    foreach ($projEntries as $e) { $entByDate[$e['entry_date']] = $e; }
    $rowTotal = 0;
    ?>
    <tr>
        <td class="ps-3 text-muted small">
            <?php $taskNames = array_unique(array_filter(array_column($projEntries,'task_title'))); ?>
            <?= $taskNames ? esc(implode(', ', array_slice($taskNames,0,2))) : '<span class="text-muted">General</span>' ?>
        </td>
        <?php foreach ($days as $day):
            $e = $entByDate[$day] ?? null;
            $h = $e ? (float)$e['hours'] : 0;
            $entId = $e ? $e['id'] : 'new';
            $rowTotal += $h;
        ?>
        <td class="text-center p-1">
            <?php if ($canEdit): ?>
            <input type="number" class="form-control form-control-sm text-center hours-input"
                   data-entry-id="<?= $entId ?>"
                   data-project-id="<?= $projId ?>"
                   data-date="<?= $day ?>"
                   min="0" max="24" step="0.5"
                   value="<?= $h > 0 ? $h : '' ?>"
                   placeholder="0"
                   style="width:72px;margin:auto;"
                   oninput="markDirty()">
            <?php else: ?>
            <span class="<?= $h > 0 ? 'fw-semibold' : 'text-muted' ?>"><?= $h > 0 ? $h : '—' ?></span>
            <?php endif; ?>
        </td>
        <?php endforeach; ?>
        <td class="text-center fw-bold"><?= number_format($projTotal,1) ?></td>
    </tr>
    <?php endforeach; ?>

    <!-- Day totals row -->
    <tr class="table-light fw-semibold">
        <td class="ps-3">Daily Total</td>
        <?php foreach ($days as $day):
            $dayTotal = array_sum(array_column(array_filter($entries, fn($e) => $e['entry_date'] === $day), 'hours'));
        ?>
        <td class="text-center"><?= $dayTotal > 0 ? number_format($dayTotal,1) : '—' ?></td>
        <?php endforeach; ?>
        <td class="text-center text-primary"><?= number_format($totalHours,1) ?></td>
    </tr>
    </tbody>
</table>
</div>
</div>

<?php if ($canEdit): ?>
<div class="d-flex justify-content-end gap-2 mt-3">
    <button class="btn btn-outline-secondary" onclick="saveGrid(false)" id="saveBtn" disabled>
        <i class="fa-solid fa-floppy-disk me-1"></i>Save
    </button>
    <button class="btn btn-primary" onclick="saveAndSubmit()">
        <i class="fa-solid fa-paper-plane me-1"></i>Save &amp; Submit
    </button>
</div>
<?php endif; ?>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <form method="post" action="<?= site_url("timesheets/{$ts['id']}/reject") ?>">
    <?= csrf_field() ?>
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">Return for Revision</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
        <textarea name="reason" class="form-control" rows="3" placeholder="Explain what needs to be corrected…" required></textarea>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Return Timesheet</button>
    </div>
    </form>
</div>
</div>
</div>

<script>
let dirty = false;

function markDirty() {
    dirty = true;
    document.getElementById('saveBtn').disabled = false;
}

function collectEntries() {
    const entries = {};
    const newEntries = [];
    document.querySelectorAll('.hours-input').forEach(inp => {
        const entId    = inp.dataset.entryId;
        const hours    = parseFloat(inp.value) || 0;
        const projId   = inp.dataset.projectId;
        const date_    = inp.dataset.date;
        if (entId === 'new') {
            if (hours > 0) newEntries.push({ project_id: projId, entry_date: date_, hours, is_billable: 1 });
        } else {
            entries[entId] = { hours, is_billable: 1 };
        }
    });
    return { entries, newEntries };
}

async function saveGrid(andSubmit = false) {
    const { entries, newEntries } = collectEntries();
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    for (const [id, d] of Object.entries(entries)) {
        fd.append(`entries[${id}][hours]`,       d.hours);
        fd.append(`entries[${id}][is_billable]`, d.is_billable);
    }
    newEntries.forEach((r, i) => {
        Object.entries(r).forEach(([k, v]) => fd.append(`new_entries[${i}][${k}]`, v));
    });

    const res = await fetch(`/staging/public/timesheets/<?= $ts['id'] ?>/save`, { method:'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        dirty = false;
        document.getElementById('saveBtn').disabled = true;
        document.getElementById('totalHoursDisplay').textContent = parseFloat(data.total_hours).toFixed(1);
        if (andSubmit) submitNow();
    }
}

function saveAndSubmit() {
    if (dirty) { saveGrid(true); } else { submitNow(); }
}

function submitNow() {
    if (!confirm('Submit timesheet for approval? You cannot edit it afterwards.')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`/staging/public/timesheets/<?= $ts['id'] ?>/submit`, { method:'POST', body: fd })
        .then(() => location.reload());
}

window.addEventListener('beforeunload', e => {
    if (dirty) { e.preventDefault(); e.returnValue = ''; }
});
</script>

<?= $this->endSection() ?>
