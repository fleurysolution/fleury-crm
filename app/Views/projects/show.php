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
<?php 
$userRoleSlug = session()->get('role_slug') ?? 'employee';
$isExternal = in_array($userRoleSlug, ['subcontractor_vendor', 'client']);
$userPermissions = session()->get('user_permissions') ?? [];
$isAdmin = in_array('admin', session()->get('user_roles') ?? []);

$hasPerm = function($slug) use ($userPermissions, $isAdmin) {
    return $isAdmin || in_array($slug, $userPermissions);
};

// Define base tabs
$tabList = [
    'overview'   => ['Overview',   'fa-chart-pie'],
    'finance'    => ['Finance',    'fa-chart-line'],
    'tasks'      => ['Tasks',      'fa-list-check'],
    'kanban'     => ['Kanban',     'fa-table-columns'],
];

// Gantt / Scheduler
if (setting('module_p6_scheduler', '1') && $hasPerm('manage_p6_scheduler')) {
    $tabList['gantt'] = ['Gantt', 'fa-bars-staggered'];
}

$tabList['milestones'] = ['Milestones', 'fa-flag'];
$tabList['drawings']   = ['Drawings',   'fa-compass-drafting'];
$tabList['rfis']       = ['RFIs',       'fa-circle-question'];
$tabList['submittals'] = ['Submittals', 'fa-file-signature'];
$tabList['estimates']  = ['Estimates',  'fa-file-invoice'];

// Preconstruction & Procurement
if (setting('module_preconstruction', '1') && $hasPerm('manage_preconstruction')) {
    $tabList['procurement'] = ['Procurement', 'fa-file-contract'];
}

$tabList['field']  = ['Field App',  'fa-helmet-safety'];
$tabList['photos'] = ['Site Photos', 'fa-images'];
$tabList['change_management'] = ['Change Management', 'fa-file-invoice-dollar'];
$tabList['meetings'] = ['Meetings', 'fa-users-rectangle'];
$tabList['areas']    = ['Areas / Zones', 'fa-layer-group'];

// Digital Handover
if (setting('module_handover_qc', '1') && $hasPerm('manage_handover_qc')) {
    $tabList['handover'] = ['Handover', 'fa-vault'];
}

$tabList['drivers'] = ['Drivers / Qty', 'fa-gauge-high'];

// Production Control
if (setting('module_production_control', '1') && $hasPerm('view_production_control')) {
    $tabList['production_control'] = ['Production & Control', 'fa-gauge-high'];
}

$tabList['files']   = ['Files',   'fa-folder-open'];
$tabList['members'] = ['Team',    'fa-users'];

// Filter for external users
if ($isExternal) {
    $allowedExternalTabs = ['overview', 'drawings', 'rfis', 'submittals', 'field', 'photos', 'files', 'finance'];
    foreach (array_keys($tabList) as $key) {
        if (!in_array($key, $allowedExternalTabs)) {
            unset($tabList[$key]);
        }
    }
}
?>

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
    case 'drawings':   include __DIR__ . '/tabs/drawings_inline.php'; break;
    case 'rfis':       include __DIR__ . '/tabs/rfis_inline.php'; break;
    case 'submittals': include __DIR__ . '/tabs/submittals_inline.php'; break;
    case 'estimates':  include __DIR__ . '/tabs/estimates_inline.php'; break;
    case 'procurement':include __DIR__ . '/tabs/procurement_inline.php'; break;
    case 'field':      include __DIR__ . '/tabs/field_inline.php'; break;
    case 'photos':     include __DIR__ . '/tabs/site_photos_inline.php'; break;
    case 'contracts':  include __DIR__ . '/tabs/contracts_inline.php'; break;
    case 'boq':        include __DIR__ . '/tabs/boq_inline.php'; break;
    case 'finance':    
        include __DIR__ . '/tabs/finance_inline.php'; 
        break;
    case 'finance_analytics':
        $budget = $budget_data;
        include __DIR__ . '/tabs/finance_analytics.php'; 
        break;
    case 'report':     include __DIR__ . '/tabs/report_inline.php'; break;
    case 'activity':   include __DIR__ . '/tabs/activity_inline.php'; break;
    case 'areas':      include __DIR__ . '/tabs/areas_inline.php'; break;
    case 'members':    include __DIR__ . '/tabs/members_inline.php'; break;
    case 'files':      include __DIR__ . '/tabs/files_inline.php'; break;
    case 'schedule':   include __DIR__ . '/tabs/schedule_inline.php'; break;
    case 'change_management': include __DIR__ . '/tabs/change_management.php'; break;
    case 'meetings':   include __DIR__ . '/tabs/meetings.php'; break;
    case 'bidding':    include __DIR__ . '/tabs/bidding.php'; break;
    case 'finance_wip': include __DIR__ . '/tabs/finance_wip.php'; break;
    case 'drivers':     include __DIR__ . '/tabs/drivers_inline.php'; break;
    case 'execution':   include __DIR__ . '/tabs/execution_inline.php'; break;
    case 'handover':    include __DIR__ . '/tabs/handover_inline.php'; break;
    case 'production_control': include __DIR__ . '/tabs/production_control.php'; break;
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
                <input type="text" id="newTaskTitle" class="form-control form-control-lg fw-semibold" placeholder="Task Title (e.g., Pour Foundation)">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select id="newTaskStatus" class="form-select form-select-sm">
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="done">Done</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Priority</label>
                <select id="newTaskPriority" class="form-select form-select-sm">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Phase</label>
                <select id="newTaskPhase" class="form-select form-select-sm">
                    <option value="">None</option>
                    <?php foreach ($phases as $ph): ?>
                    <option value="<?= $ph['id'] ?>"><?= esc($ph['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Milestone</label>
                <select id="newTaskMilestone" class="form-select form-select-sm">
                    <option value="">None</option>
                    <?php foreach ($milestones as $ms): if(!is_array($ms)) continue; ?>
                    <option value="<?= $ms['id'] ?>"><?= esc($ms['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Row 3: Assignee & Collaborators -->
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted mb-1">Assignee</label>
                <select id="newTaskAssignee" class="form-select form-select-sm">
                    <option value="">Unassigned</option>
                    <?php foreach ($members as $mem): ?>
                    <option value="<?= $mem['user_id'] ?>"><?= esc($mem['name']) ?> (<?= esc($mem['role']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-7">
                <label class="form-label small fw-semibold text-muted mb-1">Collaborators (Hold Ctrl to select multiple)</label>
                <select id="newTaskCollaborators" class="form-select form-select-sm" multiple size="2">
                    <?php foreach ($members as $mem): ?>
                    <option value="<?= $mem['user_id'] ?>"><?= esc($mem['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Row 4: Timeline -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Start Date</label>
                <input type="date" id="newTaskStart" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Start Time</label>
                <input type="time" id="newTaskStartTime" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Deadline Date</label>
                <input type="date" id="newTaskDue" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Deadline Time</label>
                <input type="time" id="newTaskEndTime" class="form-control form-control-sm">
            </div>

            <!-- Row 5: Metadata -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Recurring</label>
                <select id="newTaskRecurring" class="form-select form-select-sm">
                    <option value="">None</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Points</label>
                <input type="number" id="newTaskPoints" class="form-control form-control-sm" placeholder="0" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Est. Hours</label>
                <input type="number" id="newTaskHours" class="form-control form-control-sm" placeholder="0" min="0" step="0.5">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Labels (comma sep)</label>
                <input type="text" id="newTaskLabels" class="form-control form-control-sm" placeholder="e.g. bug, ui">
            </div>
            <div class="col-12 mt-3">
                <label class="form-label small fw-semibold text-muted mb-1">Description</label>
                <div id="newTaskDesc" style="height: 120px; background: #fff; border-radius: 0 0 4px 4px;"></div>
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

let quillNewTask, quillComment;

document.addEventListener("DOMContentLoaded", () => {
    quillNewTask = new Quill('#newTaskDesc', {
        theme: 'snow',
        placeholder: 'Optional description…',
        modules: { toolbar: [['bold', 'italic', 'underline', 'strike'], [{ 'list': 'ordered'}, { 'list': 'bullet' }], ['clean']] }
    });
});

function openNewTaskModal() {
    quillNewTask.setContents([]);
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
    fd.append('milestone_id',     document.getElementById('newTaskMilestone').value);
    fd.append('assigned_to',      document.getElementById('newTaskAssignee').value);
    fd.append('start_date',       document.getElementById('newTaskStart').value);
    fd.append('start_time',       document.getElementById('newTaskStartTime').value);
    fd.append('due_date',         document.getElementById('newTaskDue').value);
    fd.append('end_time',         document.getElementById('newTaskEndTime').value);
    fd.append('recurring_rule',   document.getElementById('newTaskRecurring').value);
    fd.append('points',           document.getElementById('newTaskPoints').value);
    fd.append('labels',           document.getElementById('newTaskLabels').value);
    fd.append('estimated_hours',  document.getElementById('newTaskHours').value);

    const collabs = document.getElementById('newTaskCollaborators');
    if (collabs) {
        Array.from(collabs.selectedOptions).forEach(opt => {
            fd.append('collaborators[]', opt.value);
        });
    }
    const descHTML = quillNewTask.root.innerHTML;
    fd.append('description', descHTML === '<p><br></p>' ? '' : descHTML);

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

    document.getElementById('taskDetailTitle').innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <span id="display-title-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'title')">${escHtml(t.title)} <i class="fa-solid fa-pen fa-xs text-muted ms-1 opacity-50"></i></span>
            <input type="text" id="input-title-${t.id}" class="form-control form-control-sm d-none" value="${escHtml(t.title)}" onblur="saveTaskField(${t.id}, 'title')" onkeydown="if(event.key==='Enter') saveTaskField(${t.id}, 'title')">
        </div>
    `;

    const chkHtml = (d.checklists||[]).map(c => `
        <div class="form-check mb-1" id="chk-${c.id}">
            <input class="form-check-input" type="checkbox" ${c.is_done?'checked':''} onchange="toggleChecklist(${t.id}, ${c.id})">
            <label class="form-check-label ${c.is_done?'text-decoration-line-through text-muted':''}">${escHtml(c.item_text)}</label>
        </div>`).join('');

    const commHtml = (d.comments||[]).map(c => `
        <div class="d-flex gap-2 mb-3">
            <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem;flex-shrink:0;">${(c.author_name||'?')[0].toUpperCase()}</div>
            <div class="flex-grow-1">
                <div class="fw-semibold small">${escHtml(c.author_name||'Unknown')}</div>
                <div class="small text-muted mb-1">${c.created_at||''}</div>
                <div class="p-2 bg-light rounded small">${escHtml(c.body)}</div>
                ${c.attachment_path ? 
                    (c.attachment_path.match(/\.(jpg|jpeg|png|gif)$/i) ? 
                        `<div class="mt-2"><img src="/staging/public/${c.attachment_path}" alt="attachment" style="max-width:100%;border-radius:5px;"></div>` : 
                        `<div class="mt-2"><a href="/staging/public/${c.attachment_path}" target="_blank" class="small"><i class="fa-solid fa-paperclip"></i> ${escHtml(c.attachment_name)}</a></div>`
                    ) : ''}
            </div>
        </div>`).join('') || '<p class="text-muted small">No comments yet.</p>';

    document.getElementById('taskDetailBody').innerHTML = `
    <div class="row g-0">
        <div class="col-lg-8 p-4 border-end">
            <div class="mb-4">${t.description||'<p class="text-muted">No description.</p>'}</div>
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-shield-check me-2"></i>Preventative QC Checklist</h6>
                <span class="badge bg-primary-subtle text-primary small">First-Time Quality</span>
            </div>
            <div id="qaChecklistArea" class="bg-light p-3 rounded mb-3">
                ${(d.qa_checklists||[]).map(q => `
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="form-check">
                            <input class="form-check-input border-primary" type="checkbox" ${q.passed?'checked':''} onchange="toggleQaItem(${t.id}, ${q.id})">
                            <label class="form-check-label fw-bold">${escHtml(q.title)}</label>
                        </div>
                        ${q.requires_photo ? '<span class="text-muted" style="font-size:0.7rem;"><i class="fa-solid fa-camera me-1"></i>Photo Required</span>' : ''}
                    </div>
                `).join('') || '<p class="text-muted small mb-0">No QC requirements defined for this task category.</p>'}
            </div>
            <hr>
            <h6 class="fw-semibold mb-2">Standard Checklist</h6>
            <div id="checklistItems">${chkHtml}</div>
            <div class="input-group mt-2" style="max-width:340px;">
                <input type="text" id="newChkItem" class="form-control form-control-sm" placeholder="Add item…">
                <button class="btn btn-sm btn-outline-primary" onclick="addChecklistItem(${t.id})">Add</button>
            </div>
            <hr>
            <h6 class="fw-semibold mb-2">Attachments</h6>
            <div id="attachmentsArea" class="d-flex flex-wrap gap-2 mb-2">${(d.attachments||[]).map(a => `
                <div class="border rounded p-2 text-center" style="width:100px;">
                    ${a.filepath.match(/\.(jpg|jpeg|png|gif)$/i) ? `<img src="/staging/public/${a.filepath}" style="width:100%;height:60px;object-fit:cover;border-radius:4px;margin-bottom:5px;">` : `<i class="fa-solid fa-file fa-2x text-muted my-2"></i>`}
                    <div class="small text-truncate" title="${escHtml(a.filename)}"><a href="/staging/public/${a.filepath}" target="_blank">${escHtml(a.filename)}</a></div>
                </div>
            `).join('')}</div>
            <div class="input-group mt-2">
                <input type="file" id="taskFile" class="form-control form-control-sm" multiple>
                <button class="btn btn-sm btn-outline-primary" onclick="uploadTaskFile(${t.id})">Upload</button>
            </div>
            <hr>
            <h6 class="fw-semibold mb-2">Comments</h6>
            <div id="commentsArea">${commHtml}</div>
            <div class="mt-3 border rounded">
                <div id="newComment" style="height: 80px; border:none; border-bottom:1px solid #dee2e6;"></div>
                <div class="p-2 bg-light d-flex align-items-center justify-content-between">
                    <input type="file" id="commentFile" class="form-control form-control-sm border-0" style="max-width:200px; background:transparent;" title="Attach Image/File">
                    <button class="btn btn-sm btn-primary" onclick="submitComment(${t.id})">Post Comment</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4 p-4">
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">Status</dt>
                <dd class="col-7"><span class="badge bg-primary-subtle text-primary">${statusLabels[t.status]||t.status}</span></dd>
                <dt class="col-5 text-muted">Priority</dt>
                <dd class="col-7"><span class="badge bg-${priorityColors[t.priority]||'secondary'}-subtle text-${priorityColors[t.priority]||'secondary'}">${t.priority}</span></dd>
                <dt class="col-5 text-muted">Assignee</dt>
                <dd class="col-7">
                    <span id="display-assigned_to-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'assigned_to')">${escHtml(t.assignee_name||'Unassigned')} <i class="fa-solid fa-pen fa-xs text-muted ms-1 opacity-50"></i></span>
                    <select id="input-assigned_to-${t.id}" class="form-select form-select-sm d-none" onblur="saveTaskField(${t.id}, 'assigned_to')">
                        <option value="">Unassigned</option>
                        ${(d.users||[]).map(u => `<option value="${u.id}" ${u.id==t.assigned_to?'selected':''}>${escHtml(u.name)}</option>`).join('')}
                    </select>
                </dd>
                
                <dt class="col-5 text-muted mt-2">Start Date</dt>
                <dd class="col-7 mt-2">
                    <span id="display-start_date-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'start_date')">${t.start_date ? dateFormatted(t.start_date) : '—'}</span>
                    <input type="date" id="input-start_date-${t.id}" class="form-control form-control-sm d-none" value="${t.start_date||''}" onblur="saveTaskField(${t.id}, 'start_date')">
                </dd>
                <dt class="col-5 text-muted">Start Time</dt>
                <dd class="col-7">
                    <span id="display-start_time-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'start_time')">${t.start_time || '—'}</span>
                    <input type="time" id="input-start_time-${t.id}" class="form-control form-control-sm d-none" value="${t.start_time||''}" onblur="saveTaskField(${t.id}, 'start_time')">
                </dd>
                
                <dt class="col-5 text-muted mt-2">Deadline Date</dt>
                <dd class="col-7 mt-2">
                    <span id="display-due_date-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'due_date')">${t.due_date ? dateFormatted(t.due_date) : '—'} <i class="fa-solid fa-pen fa-xs text-muted ms-1 opacity-50"></i></span>
                    <input type="date" id="input-due_date-${t.id}" class="form-control form-control-sm d-none" value="${t.due_date||''}" onblur="saveTaskField(${t.id}, 'due_date')">
                </dd>
                <dt class="col-5 text-muted">Deadline Time</dt>
                <dd class="col-7">
                    <span id="display-end_time-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'end_time')">${t.end_time || '—'}</span>
                    <input type="time" id="input-end_time-${t.id}" class="form-control form-control-sm d-none" value="${t.end_time||''}" onblur="saveTaskField(${t.id}, 'end_time')">
                </dd>

                <dt class="col-5 text-muted mt-2">Est. Hours</dt>
                <dd class="col-7 mt-2">
                    <span id="display-estimated_hours-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'estimated_hours')">${t.estimated_hours||'—'}</span>
                    <input type="number" id="input-estimated_hours-${t.id}" class="form-control form-control-sm d-none" value="${t.estimated_hours||''}" onblur="saveTaskField(${t.id}, 'estimated_hours')">
                </dd>
                <dt class="col-5 text-muted">Points</dt>
                <dd class="col-7">
                    <span id="display-points-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'points')">${t.points||'0'}</span>
                    <input type="number" id="input-points-${t.id}" class="form-control form-control-sm d-none" value="${t.points||'0'}" onblur="saveTaskField(${t.id}, 'points')">
                </dd>
                
                <dt class="col-5 text-muted mt-2">Labels</dt>
                <dd class="col-7 mt-2">
                    <span id="display-labels-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'labels')">${escHtml(t.labels||'—')}</span>
                    <input type="text" id="input-labels-${t.id}" class="form-control form-control-sm d-none" value="${escHtml(t.labels||'')}" onblur="saveTaskField(${t.id}, 'labels')" placeholder="tag, tag2">
                </dd>
                
                <dt class="col-5 text-muted">Recurring</dt>
                <dd class="col-7">
                    <span id="display-recurring_rule-${t.id}" class="task-editable" onclick="editTaskField(${t.id}, 'recurring_rule')">${escHtml(t.recurring_rule||'None')}</span>
                    <select id="input-recurring_rule-${t.id}" class="form-select form-select-sm d-none" onblur="saveTaskField(${t.id}, 'recurring_rule')">
                        <option value="">None</option>
                        <option value="daily" ${t.recurring_rule==='daily'?'selected':''}>Daily</option>
                        <option value="weekly" ${t.recurring_rule==='weekly'?'selected':''}>Weekly</option>
                        <option value="monthly" ${t.recurring_rule==='monthly'?'selected':''}>Monthly</option>
                    </select>
                </dd>

                <dt class="col-5 text-muted mt-2">Progress</dt>
                <dd class="col-7 mt-2">${t.percent_complete||0}%</dd>
                
                <dt class="col-12 text-muted mt-3 mb-1 border-top pt-2">Collaborators</dt>
                <dd class="col-12" style="font-size:0.8rem;">
                    ${(d.collaborators||[]).length > 0 ? (d.collaborators).map(c=>`<span class="badge bg-light text-dark border me-1">${escHtml(c.first_name)} ${escHtml(c.last_name)}</span>`).join('') : '<span class="text-muted">None</span>'}
                </dd>
            </dl>
            <div class="mt-3">
                <label class="form-label small fw-semibold">Update Status</label>
                <select class="form-select form-select-sm" onchange="updateTaskStatus(${t.id}, this.value)">
                    ${['todo','in_progress','review','done','blocked'].map(s=>`<option value="${s}" ${s===t.status?'selected':''}>${statusLabels[s]}</option>`).join('')}
                </select>
            </div>
        </div>
    </div>`;

    setTimeout(() => {
        quillComment = new Quill('#newComment', {
            theme: 'snow',
            placeholder: 'Write a comment…',
            modules: { toolbar: [['bold', 'italic', 'underline', 'strike'], ['link'], [{ 'list': 'ordered'}, { 'list': 'bullet' }]] }
        });
    }, 50);
}

function toggleChecklist(taskId, itemId) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('item_id', itemId);
    fd.append('action', 'toggle');
    fetch(`/staging/public/tasks/${taskId}/checklist`, { method:'POST', body: fd });
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
                    `<div class="form-check mb-1"><input class="form-check-input" type="checkbox" onchange="toggleChecklist(${taskId}, ${d.item.id})"><label class="form-check-label">${escHtml(d.item.item_text)}</label></div>`);
                document.getElementById('newChkItem').value = '';
            }
        });
}

function submitComment(taskId) {
    const bodyHTML = quillComment.root.innerHTML;
    const body = bodyHTML === '<p><br></p>' ? '' : bodyHTML;
    const fileInput = document.getElementById('commentFile');
    if (!body && fileInput.files.length === 0) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('body', body);
    if (fileInput.files.length > 0) fd.append('attachment', fileInput.files[0]);
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
                            ${c.attachment_path ? 
                                (c.attachment_path.match(/\.(jpg|jpeg|png|gif)$/i) ? 
                                    `<div class="mt-2"><img src="/staging/public/${c.attachment_path}" alt="attachment" style="max-width:100%;border-radius:5px;"></div>` : 
                                    `<div class="mt-2"><a href="/staging/public/${c.attachment_path}" target="_blank" class="small"><i class="fa-solid fa-paperclip"></i> ${escHtml(c.attachment_name)}</a></div>`
                                ) : ''}
                        </div>
                     </div>`);
                document.getElementById('newComment').value = '';
                fileInput.value = '';
            }
        });
}

function uploadTaskFile(taskId) {
    const fileInput = document.getElementById('taskFile');
    if (fileInput.files.length === 0) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    for (let i = 0; i < fileInput.files.length; i++) {
        fd.append('files[]', fileInput.files[i]);
    }
    fetch(`/staging/public/tasks/${taskId}/upload`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{
            if (d.success && d.attachments) {
                const area = document.getElementById('attachmentsArea');
                d.attachments.forEach(a => {
                    area.insertAdjacentHTML('beforeend',
                        `<div class="border rounded p-2 text-center" style="width:100px;">
                            ${a.filepath.match(/\.(jpg|jpeg|png|gif)$/i) ? `<img src="/staging/public/${a.filepath}" style="width:100%;height:60px;object-fit:cover;border-radius:4px;margin-bottom:5px;">` : `<i class="fa-solid fa-file fa-2x text-muted my-2"></i>`}
                            <div class="small text-truncate" title="${escHtml(a.filename)}"><a href="/staging/public/${a.filepath}" target="_blank">${escHtml(a.filename)}</a></div>
                        </div>`
                    );
                });
                fileInput.value = '';
            } else {
                alert(d.message || "Failed to upload files.");
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
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function dateFormatted(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString();
}

function editTaskField(taskId, field) {
    document.getElementById(`display-${field}-${taskId}`).classList.add('d-none');
    const input = document.getElementById(`input-${field}-${taskId}`);
    input.classList.remove('d-none');
    input.focus();
}

function saveTaskField(taskId, field) {
    const input = document.getElementById(`input-${field}-${taskId}`);
    const display = document.getElementById(`display-${field}-${taskId}`);
    const val = input.value.trim();
    
    // Optimistic UI Update
    input.classList.add('d-none');
    display.classList.remove('d-none');
    
    if (field === 'title') { display.innerHTML = `${escHtml(val)} <i class="fa-solid fa-pen fa-xs text-muted ms-1 opacity-50"></i>`; }
    else if (field === 'assigned_to') { display.innerHTML = `${escHtml(input.options[input.selectedIndex].text)} <i class="fa-solid fa-pen fa-xs text-muted ms-1 opacity-50"></i>`; }
    else if (field === 'due_date' || field === 'start_date') { display.innerHTML = `${val ? dateFormatted(val) : '—'} <i class="fa-solid fa-pen fa-xs text-muted ms-1 opacity-50"></i>`; }
    else if (field === 'recurring_rule') {
        const textObj = {daily:'Daily', weekly:'Weekly', monthly:'Monthly'};
        display.innerHTML = `${textObj[val] || 'None'}`;
    }
    else { display.innerHTML = `${escHtml(val) || '—'}`; }

    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append(field, val);
    
    fetch(`/staging/public/tasks/${taskId}/update`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{
            if(!d.success) alert('Failed to update task.');
        });
}

function toggleQaItem(taskId, itemId) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('item_id', itemId);
    fetch(`/staging/public/tasks/${taskId}/qa-toggle`, { method:'POST', body: fd });
}
</script>

<style>
.task-editable { cursor: pointer; padding: 2px 4px; border-radius: 4px; transition: background 0.2s; }
.task-editable:hover { background: rgba(0,0,0,0.05); }
</style>

<?= $this->endSection() ?>
