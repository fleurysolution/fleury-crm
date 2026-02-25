<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php
$statusBadge = ['draft'=>'secondary','active'=>'success','on_hold'=>'warning','completed'=>'primary','archived'=>'dark'][$project['status']] ?? 'secondary';
$priorityBadge = ['low'=>'info','medium'=>'secondary','high'=>'warning','urgent'=>'danger'][$project['priority']] ?? 'secondary';
?>

<!-- Project Header -->
<div class="project-workspace-header d-flex align-items-start justify-content-between mb-0 pb-3 border-bottom">
    <div class="d-flex align-items-center gap-3">
        <div class="project-color-dot" style="width:14px;height:14px;border-radius:50%;background:<?= esc($project['color']) ?>;flex-shrink:0;margin-top:5px;"></div>
        <div>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= site_url('projects') ?>" class="text-decoration-none">Projects</a></li>
                <li class="breadcrumb-item active"><?= esc($project['title']) ?></li>
            </ol></nav>
            <h4 class="fw-bold mb-1"><?= esc($project['title']) ?></h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-<?= $statusBadge ?>-subtle text-<?= $statusBadge ?>"><?= ucfirst(str_replace('_',' ',$project['status'])) ?></span>
                <span class="badge bg-<?= $priorityBadge ?>-subtle text-<?= $priorityBadge ?>"><?= ucfirst($project['priority']) ?></span>
                <?php if ($project['client_name']): ?>
                <span class="small text-muted"><i class="fa-solid fa-building me-1"></i><?= esc($project['client_name']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url("projects/{$project['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-pen me-1"></i>Settings
        </a>
        <button class="btn btn-sm btn-primary" onclick="openNewTaskModal()">
            <i class="fa-solid fa-plus me-1"></i>Add Task
        </button>
    </div>
</div>

<!-- Stats bar -->
<div class="row g-3 my-2">
    <?php
    $statCards = [
        ['val'=>$stats['total'],       'label'=>'Total Tasks',    'icon'=>'fa-list-check',         'color'=>'primary'],
        ['val'=>$stats['done'],        'label'=>'Completed',      'icon'=>'fa-circle-check',        'color'=>'success'],
        ['val'=>$stats['in_progress'], 'label'=>'In Progress',    'icon'=>'fa-spinner',             'color'=>'info'],
        ['val'=>$stats['blocked'],     'label'=>'Blocked',        'icon'=>'fa-ban',                 'color'=>'danger'],
        ['val'=>$stats['percent'].'%', 'label'=>'Complete',       'icon'=>'fa-chart-pie',           'color'=>'warning'],
        ['val'=>count($members),       'label'=>'Members',        'icon'=>'fa-users',               'color'=>'secondary'],
    ];
    foreach ($statCards as $sc): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 bg-<?= $sc['color'] ?>-subtle text-center py-2 px-1 h-100" style="border-radius:10px;">
            <i class="fa-solid <?= $sc['icon'] ?> text-<?= $sc['color'] ?> mb-1"></i>
            <div class="fw-bold fs-5 text-<?= $sc['color'] ?>"><?= $sc['val'] ?></div>
            <div class="text-muted" style="font-size:.72rem;"><?= $sc['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabs -->
<?php $tabList = [
    'overview'   => ['Overview',   'fa-gauge'],
    'tasks'      => ['Tasks',      'fa-list-check'],
    'kanban'     => ['Kanban',     'fa-table-columns'],
    'gantt'      => ['Gantt',      'fa-bars-staggered'],
    'milestones' => ['Milestones', 'fa-flag'],
    'rfis'       => ['RFIs',       'fa-circle-question'],
    'punch_list' => ['Punch List', 'fa-clipboard-check'],
    'site_diary' => ['Site Diary', 'fa-book-open'],
    'contracts'  => ['Contracts',  'fa-file-contract'],
    'boq'        => ['BOQ',        'fa-table-list'],
    'finance'    => ['Finance',    'fa-coins'],
    'report'     => ['Report',     'fa-chart-bar'],
    'activity'   => ['Activity',   'fa-clock-rotate-left'],
    'areas'      => ['Areas',      'fa-sitemap'],
    'schedule'   => ['Schedule',   'fa-calendar-day'],
    'files'      => ['Files',      'fa-folder-open'],
    'members'    => ['Team',       'fa-users'],
]; ?>
<ul class="nav nav-tabs mb-3 border-bottom" id="projectTabs">
    <?php foreach ($tabList as $slug => [$label, $icon]): ?>
    <li class="nav-item">
        <a class="nav-link <?= $tab === $slug ? 'active fw-semibold' : '' ?>"
           href="<?= site_url("projects/{$project['id']}?tab={$slug}") ?>">
            <i class="fa-solid <?= $icon ?> me-1"></i><?= $label ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Tab Content -->
<div id="tab-content">
<?php switch ($tab):
    case 'tasks':      include __DIR__ . '/tabs/tasks_list_inline.php'; break;
    case 'kanban':     include __DIR__ . '/tabs/kanban_inline.php'; break;
    case 'gantt':      include __DIR__ . '/tabs/gantt_inline.php'; break;
    case 'milestones': include __DIR__ . '/tabs/milestones_inline.php'; break;
    case 'rfis':       include __DIR__ . '/tabs/rfis_inline.php'; break;
    case 'punch_list': include __DIR__ . '/tabs/punch_list_inline.php'; break;
    case 'site_diary': include __DIR__ . '/tabs/site_diary_inline.php'; break;
    case 'contracts':  include __DIR__ . '/tabs/contracts_inline.php'; break;
    case 'boq':        include __DIR__ . '/tabs/boq_inline.php'; break;
    case 'finance':    include __DIR__ . '/tabs/finance_inline.php'; break;
    case 'report':     include __DIR__ . '/tabs/report_inline.php'; break;
    case 'activity':   include __DIR__ . '/tabs/activity_inline.php'; break;
    case 'areas':      include __DIR__ . '/tabs/areas_inline.php'; break;
    case 'members':    include __DIR__ . '/tabs/members_inline.php'; break;
    case 'files':      include __DIR__ . '/tabs/files_inline.php'; break;
    case 'schedule':   include __DIR__ . '/tabs/schedule_inline.php'; break;
    default:           include __DIR__ . '/tabs/overview_inline.php'; break;
endswitch; ?>
</div>

<!-- New Task Modal -->
<div class="modal fade" id="newTaskModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <input type="text" id="newTaskTitle" class="form-control form-control-lg" placeholder="Task title…">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Phase</label>
                <select id="newTaskPhase" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($phases as $ph): ?>
                    <option value="<?= $ph['id'] ?>"><?= esc($ph['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status</label>
                <select id="newTaskStatus" class="form-select">
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="done">Done</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Priority</label>
                <select id="newTaskPriority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Due Date</label>
                <input type="date" id="newTaskDue" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Est. Hours</label>
                <input type="number" id="newTaskHours" class="form-control" placeholder="0" min="0" step="0.5">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Description</label>
                <textarea id="newTaskDesc" class="form-control" rows="3" placeholder="Optional description…"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitNewTask()">
            <i class="fa-solid fa-check me-1"></i> Create Task
        </button>
    </div>
</div>
</div>
</div>

<!-- Task Detail Modal (loaded via AJAX) -->
<div class="modal fade" id="taskDetailModal" tabindex="-1">
<div class="modal-dialog modal-xl">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold" id="taskDetailTitle">Task Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-0" id="taskDetailBody" style="min-height:300px;">
        <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
    </div>
</div>
</div>
</div>

<script>
const PROJECT_ID = <?= $project['id'] ?>;
const CSRF_TOKEN = '<?= csrf_hash() ?>';
const CSRF_NAME  = '<?= csrf_token() ?>';

function openNewTaskModal() {
    new bootstrap.Modal(document.getElementById('newTaskModal')).show();
}

function submitNewTask() {
    const title = document.getElementById('newTaskTitle').value.trim();
    if (!title) { alert('Please enter a task title.'); return; }

    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('title',            title);
    fd.append('phase_id',         document.getElementById('newTaskPhase').value);
    fd.append('status',           document.getElementById('newTaskStatus').value);
    fd.append('priority',         document.getElementById('newTaskPriority').value);
    fd.append('due_date',         document.getElementById('newTaskDue').value);
    fd.append('estimated_hours',  document.getElementById('newTaskHours').value);
    fd.append('description',      document.getElementById('newTaskDesc').value);

    fetch(`/staging/public/projects/${PROJECT_ID}/tasks`, { method:'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) { location.reload(); }
            else alert('Could not create task.');
        });
}

function openTask(id) {
    const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
    document.getElementById('taskDetailBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    modal.show();
    fetch(`/staging/public/tasks/${id}`, { headers: {'X-Requested-With':'XMLHttpRequest'} })
        .then(r => r.json())
        .then(d => {
            if (d.success) renderTaskDetail(d);
        });
}

function renderTaskDetail(d) {
    const t = d.task;
    const priorityColors = {low:'info',medium:'secondary',high:'warning',urgent:'danger'};
    const statusLabels   = {todo:'To Do',in_progress:'In Progress',review:'Review',done:'Done',blocked:'Blocked'};

    document.getElementById('taskDetailTitle').textContent = t.title;

    const chkHtml = (d.checklists||[]).map(c => `
        <div class="form-check mb-1" id="chk-${c.id}">
            <input class="form-check-input" type="checkbox" ${c.is_done?'checked':''} onchange="toggleChecklist(${c.id})">
            <label class="form-check-label ${c.is_done?'text-decoration-line-through text-muted':''}">${escHtml(c.item_text)}</label>
        </div>`).join('');

    const commHtml = (d.comments||[]).map(c => `
        <div class="d-flex gap-2 mb-3">
            <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem;flex-shrink:0;">${(c.author_name||'?')[0].toUpperCase()}</div>
            <div class="flex-grow-1">
                <div class="fw-semibold small">${escHtml(c.author_name||'Unknown')}</div>
                <div class="small text-muted mb-1">${c.created_at||''}</div>
                <div class="p-2 bg-light rounded small">${escHtml(c.body)}</div>
            </div>
        </div>`).join('') || '<p class="text-muted small">No comments yet.</p>';

    document.getElementById('taskDetailBody').innerHTML = `
    <div class="row g-0">
        <div class="col-lg-8 p-4 border-end">
            <p class="text-muted">${escHtml(t.description||'No description.')}</p>
            <hr>
            <h6 class="fw-semibold mb-2">Checklist</h6>
            <div id="checklistItems">${chkHtml}</div>
            <div class="input-group mt-2" style="max-width:340px;">
                <input type="text" id="newChkItem" class="form-control form-control-sm" placeholder="Add item…">
                <button class="btn btn-sm btn-outline-primary" onclick="addChecklistItem(${t.id})">Add</button>
            </div>
            <hr>
            <h6 class="fw-semibold mb-2">Comments</h6>
            <div id="commentsArea">${commHtml}</div>
            <div class="input-group mt-2">
                <textarea id="newComment" class="form-control form-control-sm" rows="2" placeholder="Write a comment…"></textarea>
                <button class="btn btn-sm btn-primary" onclick="submitComment(${t.id})">Post</button>
            </div>
        </div>
        <div class="col-lg-4 p-4">
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">Status</dt>
                <dd class="col-7"><span class="badge bg-primary-subtle text-primary">${statusLabels[t.status]||t.status}</span></dd>
                <dt class="col-5 text-muted">Priority</dt>
                <dd class="col-7"><span class="badge bg-${priorityColors[t.priority]||'secondary'}-subtle text-${priorityColors[t.priority]||'secondary'}">${t.priority}</span></dd>
                <dt class="col-5 text-muted">Assignee</dt>
                <dd class="col-7">${escHtml(t.assignee_name||'—')}</dd>
                <dt class="col-5 text-muted">Due Date</dt>
                <dd class="col-7">${t.due_date||'—'}</dd>
                <dt class="col-5 text-muted">Est. Hours</dt>
                <dd class="col-7">${t.estimated_hours||'—'}</dd>
                <dt class="col-5 text-muted">Progress</dt>
                <dd class="col-7">${t.percent_complete||0}%</dd>
            </dl>
            <div class="mt-3">
                <label class="form-label small fw-semibold">Update Status</label>
                <select class="form-select form-select-sm" onchange="updateTaskStatus(${t.id}, this.value)">
                    ${['todo','in_progress','review','done','blocked'].map(s=>`<option value="${s}" ${s===t.status?'selected':''}>${statusLabels[s]}</option>`).join('')}
                </select>
            </div>
        </div>
    </div>`;
}

function toggleChecklist(itemId) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('item_id', itemId);
    fd.append('action', 'toggle');
    fetch(`/staging/public/tasks/${itemId}/checklist`, { method:'POST', body: fd });
}

function addChecklistItem(taskId) {
    const text = document.getElementById('newChkItem').value.trim();
    if (!text) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('action', 'add');
    fd.append('text', text);
    fetch(`/staging/public/tasks/${taskId}/checklist`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{
            if (d.success) {
                document.getElementById('checklistItems').insertAdjacentHTML('beforeend',
                    `<div class="form-check mb-1"><input class="form-check-input" type="checkbox" onchange="toggleChecklist(${d.item.id})"><label class="form-check-label">${escHtml(d.item.item_text)}</label></div>`);
                document.getElementById('newChkItem').value = '';
            }
        });
}

function submitComment(taskId) {
    const body = document.getElementById('newComment').value.trim();
    if (!body) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('body', body);
    fetch(`/staging/public/tasks/${taskId}/comment`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{
            if (d.success) {
                const c = d.comment;
                document.getElementById('commentsArea').insertAdjacentHTML('beforeend',
                    `<div class="d-flex gap-2 mb-3">
                        <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem;flex-shrink:0;">${(c.author_name||'?')[0].toUpperCase()}</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">${escHtml(c.author_name||'Me')}</div>
                            <div class="p-2 bg-light rounded small mt-1">${escHtml(c.body)}</div>
                        </div>
                     </div>`);
                document.getElementById('newComment').value = '';
            }
        });
}

function updateTaskStatus(taskId, status) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fetch(`/staging/public/tasks/${taskId}/move`, { method:'POST', body: fd });
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?= $this->endSection() ?>
