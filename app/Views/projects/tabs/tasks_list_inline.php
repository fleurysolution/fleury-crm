<?php
// app/Views/projects/tabs/tasks_list_inline.php
$taskModel  = new \App\Models\TaskModel();
$phaseModel = new \App\Models\PhaseModel();
$userModel  = new \App\Models\UserModel();

$phases = $phaseModel->forProject($project['id']);
$tasks  = $taskModel->withAssignee()
    ->where('tasks.project_id', $project['id'])
    ->orderBy('tasks.phase_id')
    ->orderBy('tasks.sort_order')
    ->findAll();

$byPhase = [];
foreach ($phases as $p) { $byPhase[$p['id']] = ['phase' => $p, 'tasks' => []]; }
$byPhase[0] = ['phase' => ['id' => 0, 'title' => 'Uncategorised', 'color' => '#aaa'], 'tasks' => []];
foreach ($tasks as $t) {
    $key = $t['phase_id'] ?? 0;
    if (!isset($byPhase[$key])) $byPhase[$key] = ['phase' => ['id' => $key, 'title' => 'Other', 'color' => '#aaa'], 'tasks' => []];
    $byPhase[$key]['tasks'][] = $t;
}

$priorities = ['low'=>'info','medium'=>'secondary','high'=>'warning','urgent'=>'danger'];
$statuses   = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success','blocked'=>'danger'];
$statusLbls = ['todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done','blocked'=>'Blocked'];
?>

<div class="d-flex justify-content-end mb-3 gap-2">
    <button class="btn btn-sm btn-primary" onclick="openNewTaskModal()">
        <i class="fa-solid fa-plus me-1"></i>Add Task
    </button>
</div>

<?php foreach ($byPhase as $group):
    if (empty($group['tasks'])) continue;
    $ph = $group['phase'];
?>
<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:10px;height:10px;border-radius:50%;background:<?= esc($ph['color']) ?>;"></div>
        <h6 class="fw-semibold mb-0"><?= esc($ph['title']) ?></h6>
        <span class="badge bg-light text-muted"><?= count($group['tasks']) ?></span>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
        <table class="table table-hover mb-0 align-middle small">
            <thead class="table-light">
                <tr>
                    <th style="width:40%">Title</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assignee</th>
                    <th>Due</th>
                    <th>%</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($group['tasks'] as $t): ?>
            <tr class="task-row" style="cursor:pointer;" onclick="openTask(<?= $t['id'] ?>)">
                <td>
                    <span class="fw-semibold text-dark"><?= esc($t['title']) ?></span>
                    <?php if ($t['description']): ?>
                    <div class="text-muted" style="font-size:.75rem;line-clamp:1;overflow:hidden;white-space:nowrap;max-width:280px;"><?= esc($t['description']) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-<?= $statuses[$t['status']]??'secondary' ?>-subtle text-<?= $statuses[$t['status']]??'secondary' ?>">
                        <?= $statusLbls[$t['status']] ?? ucfirst($t['status']) ?>
                    </span>
                </td>
                <td>
                    <span class="badge bg-<?= $priorities[$t['priority']]??'secondary' ?>-subtle text-<?= $priorities[$t['priority']]??'secondary' ?>">
                        <?= ucfirst($t['priority']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($t['assignee_name']): ?>
                    <div class="d-flex align-items-center gap-1">
                        <div class="user-avatar" style="width:26px;height:26px;font-size:.65rem;">
                            <?= strtoupper(substr($t['assignee_name'],0,1)) ?>
                        </div>
                        <span><?= esc($t['assignee_name']) ?></span>
                    </div>
                    <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </td>
                <td><?= $t['due_date'] ? date('d M', strtotime($t['due_date'])) : '—' ?></td>
                <td>
                    <div class="progress" style="height:6px;width:50px;">
                        <div class="progress-bar bg-<?= $t['status']==='done'?'success':'primary' ?>"
                             style="width:<?= $t['percent_complete'] ?>%"></div>
                    </div>
                </td>
                <td onclick="event.stopPropagation()">
                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                        onclick="if(confirm('Delete this task?')){deleteTask(<?= $t['id'] ?>)}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($tasks)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-list-check fa-2x mb-2 opacity-25 d-block"></i>
    No tasks yet. <a href="#" onclick="openNewTaskModal()">Add the first one</a>.
</div>
<?php endif; ?>

<script>
function deleteTask(id) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`/staging/public/tasks/${id}/delete`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
