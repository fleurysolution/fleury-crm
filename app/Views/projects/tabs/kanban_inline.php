<?php
// app/Views/projects/tabs/kanban_inline.php
$taskModel = new \App\Models\TaskModel();
$cols = $taskModel->getKanbanColumns($project['id']);
$colMeta = [
    'todo'        => ['To Do',       'secondary', 'fa-circle'],
    'in_progress' => ['In Progress', 'primary',   'fa-spinner'],
    'review'      => ['Review',      'warning',   'fa-magnifying-glass'],
    'done'        => ['Done',        'success',   'fa-circle-check'],
    'blocked'     => ['Blocked',     'danger',    'fa-ban'],
];
$priorityColors = ['low'=>'info','medium'=>'secondary','high'=>'warning','urgent'=>'danger'];
?>
<style>
.kanban-board { display:flex; gap:1rem; overflow-x:auto; min-height:60vh; align-items:flex-start; padding-bottom:1rem; }
.kanban-col   { min-width:270px; max-width:270px; flex-shrink:0; }
.kanban-col-header { padding:.6rem 1rem; border-radius:8px 8px 0 0; font-size:.8rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
.kanban-cards { min-height:80px; padding:.5rem; background:#f8f9fa; border-radius:0 0 8px 8px; border:1px solid #e9ecef; border-top:none; }
.kanban-card  { background:#fff; border-radius:8px; padding:.75rem; margin-bottom:.5rem; box-shadow:0 1px 3px rgba(0,0,0,.08); cursor:pointer; transition:box-shadow .15s, transform .1s; font-size:.82rem; border-left:3px solid transparent; }
.kanban-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.12); transform:translateY(-1px); }
.kanban-card.priority-urgent { border-left-color:#dc3545; }
.kanban-card.priority-high   { border-left-color:#fd7e14; }
.kanban-card.drag-over { outline:2px dashed #4a90e2; }
</style>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-primary" onclick="openNewTaskModal()">
        <i class="fa-solid fa-plus me-1"></i>Add Task
    </button>
</div>

<div class="kanban-board" id="kanbanBoard">
<?php foreach ($colMeta as $status => [$label, $color, $icon]): ?>
<div class="kanban-col" data-status="<?= $status ?>">
    <div class="kanban-col-header bg-<?= $color ?>-subtle text-<?= $color ?>">
        <i class="fa-solid <?= $icon ?> me-2"></i><?= $label ?>
        <span class="badge bg-white text-<?= $color ?> ms-1"><?= count($cols[$status] ?? []) ?></span>
    </div>
    <div class="kanban-cards" id="col-<?= $status ?>"
         ondragover="event.preventDefault();this.classList.add('drag-over')"
         ondragleave="this.classList.remove('drag-over')"
         ondrop="onDrop(event,'<?= $status ?>')">
        <?php foreach ($cols[$status] ?? [] as $t): ?>
        <div class="kanban-card priority-<?= $t['priority'] ?>"
             draggable="true"
             data-task-id="<?= $t['id'] ?>"
             ondragstart="onDragStart(event,<?= $t['id'] ?>)"
             onclick="openTask(<?= $t['id'] ?>)">
            <div class="fw-semibold mb-1"><?= esc($t['title']) ?></div>
            <?php if ($t['due_date']): ?>
            <div class="text-muted mb-1" style="font-size:.72rem;">
                <i class="fa-regular fa-calendar me-1"></i><?= date('d M', strtotime($t['due_date'])) ?>
            </div>
            <?php endif; ?>
            <div class="d-flex align-items-center justify-content-between">
                <span class="badge bg-<?= $priorityColors[$t['priority']]??'secondary' ?>-subtle text-<?= $priorityColors[$t['priority']]??'secondary' ?>"><?= ucfirst($t['priority']) ?></span>
                <?php if ($t['assignee_name']): ?>
                <div class="user-avatar" style="width:24px;height:24px;font-size:.6rem;" title="<?= esc($t['assignee_name']) ?>">
                    <?= strtoupper(substr($t['assignee_name'],0,1)) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

<script>
let draggedTaskId = null;

function onDragStart(e, id) {
    draggedTaskId = id;
    e.dataTransfer.effectAllowed = 'move';
}

function onDrop(e, newStatus) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    if (!draggedTaskId) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', newStatus);
    fetch(`/staging/public/tasks/${draggedTaskId}/move`, { method:'POST', body: fd })
        .then(r=>r.json())
        .then(d=>{ if(d.success) location.reload(); });
    draggedTaskId = null;
}

// Clear drag-over on all cols on dragend
document.querySelectorAll('.kanban-cards').forEach(el=>{
    el.addEventListener('dragend',()=>el.classList.remove('drag-over'));
});
</script>
