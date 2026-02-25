<?php

namespace App\Controllers;

use App\Controllers\BaseAppController;
use App\Models\TaskModel;
use App\Models\TaskCommentModel;
use App\Models\TaskAttachmentModel;
use App\Models\TaskChecklistModel;
use App\Models\PhaseModel;
use App\Models\UserModel;

class Tasks extends BaseAppController
{
    protected TaskModel           $tasks;
    protected TaskCommentModel    $comments;
    protected TaskAttachmentModel $attachments;
    protected TaskChecklistModel  $checklists;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->tasks       = new TaskModel();
        $this->comments    = new TaskCommentModel();
        $this->attachments = new TaskAttachmentModel();
        $this->checklists  = new TaskChecklistModel();
    }

    // ── LIST VIEW ────────────────────────────────────────────────────────
    public function index(int $projectId): string
    {
        $phases = (new PhaseModel())->forProject($projectId);
        $tasks  = $this->tasks->withAssignee()
            ->where('tasks.project_id', $projectId)
            ->orderBy('tasks.phase_id')
            ->orderBy('tasks.sort_order')
            ->findAll();

        // Group by phase
        $byPhase = [];
        foreach ($phases as $p) { $byPhase[$p['id']] = ['phase' => $p, 'tasks' => []]; }
        $byPhase[0] = ['phase' => ['id' => 0, 'title' => 'No Phase', 'color' => '#aaa'], 'tasks' => []];
        foreach ($tasks as $t) {
            $key = $t['phase_id'] ?? 0;
            $byPhase[$key]['tasks'][] = $t;
        }

        $users = model(UserModel::class)->select('id, CONCAT(first_name, " ", last_name) AS name')->findAll();
        return view('projects/tabs/tasks_list', [
            'project_id' => $projectId,
            'byPhase'    => $byPhase,
            'users'      => $users,
        ]);
    }

    // ── KANBAN ───────────────────────────────────────────────────────────
    public function kanban(int $projectId): string
    {
        $cols  = $this->tasks->getKanbanColumns($projectId);
        $users = model(UserModel::class)->select('id, CONCAT(first_name, " ", last_name) AS name')->findAll();
        return view('projects/tabs/kanban', [
            'project_id' => $projectId,
            'cols'       => $cols,
            'users'      => $users,
        ]);
    }

    // ── CREATE (AJAX) ────────────────────────────────────────────────────
    public function store(int $projectId)
    {
        $data = [
            'project_id'  => $projectId,
            'phase_id'    => $this->request->getPost('phase_id') ?: null,
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description') ?: null,
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'status'      => $this->request->getPost('status') ?? 'todo',
            'priority'    => $this->request->getPost('priority') ?? 'medium',
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'estimated_hours' => $this->request->getPost('estimated_hours') ?: null,
            'created_by'  => session()->get('user_id'),
        ];

        if (!trim($data['title'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Title is required.']);
        }

        $id   = $this->tasks->insert($data);
        $task = $this->tasks->withAssignee()->where('tasks.id', $id)->first();
        return $this->response->setJSON(['success' => true, 'task' => $task]);
    }

    // ── SHOW (AJAX detail panel) ────────────────────────────────────────
    public function show(int $id)
    {
        $task        = $this->tasks->withAssignee()->where('tasks.id', $id)->first();
        if (!$task) return $this->response->setJSON(['success' => false]);
        $comments    = $this->comments->forTask($id);
        $attachments = $this->attachments->forTask($id);
        $checklists  = $this->checklists->forTask($id);
        $users       = model(UserModel::class)->select('id, CONCAT(first_name, " ", last_name) AS name')->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'     => true,
                'task'        => $task,
                'comments'    => $comments,
                'attachments' => $attachments,
                'checklists'  => $checklists,
            ]);
        }
        return view('projects/partials/task_modal', compact('task','comments','attachments','checklists','users'));
    }

    // ── UPDATE (AJAX) ────────────────────────────────────────────────────
    public function update(int $id)
    {
        $allowed = ['title','description','assigned_to','status','priority',
                    'start_date','due_date','estimated_hours','percent_complete',
                    'phase_id','milestone_id','area_id','cost_code_id'];
        $data = [];
        foreach ($allowed as $f) {
            $v = $this->request->getPost($f);
            if ($v !== null) { $data[$f] = $v === '' ? null : $v; }
        }
        $this->tasks->update($id, $data);
        $task = $this->tasks->withAssignee()->where('tasks.id', $id)->first();
        return $this->response->setJSON(['success' => true, 'task' => $task]);
    }

    // ── MOVE STATUS (Kanban drag) ─────────────────────────────────────────
    public function move(int $id)
    {
        $status = $this->request->getPost('status');
        $valid  = ['todo','in_progress','review','done','blocked'];
        if (!in_array($status, $valid)) {
            return $this->response->setJSON(['success' => false]);
        }
        $pct = $status === 'done' ? 100 : null;
        $upd = ['status' => $status];
        if ($pct !== null) $upd['percent_complete'] = $pct;
        $this->tasks->update($id, $upd);
        return $this->response->setJSON(['success' => true]);
    }

    // ── COMMENT ──────────────────────────────────────────────────────────
    public function comment(int $taskId)
    {
        $body = trim($this->request->getPost('body') ?? '');
        if (!$body) return $this->response->setJSON(['success' => false]);
        $id  = $this->comments->insert([
            'task_id' => $taskId,
            'user_id' => session()->get('user_id'),
            'body'    => $body,
        ]);
        $c = $this->comments->select('task_comments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name')
            ->join('fs_users','fs_users.id = task_comments.user_id','left')
            ->find($id);
        return $this->response->setJSON(['success' => true, 'comment' => $c]);
    }

    // ── UPLOAD ───────────────────────────────────────────────────────────
    public function upload(int $taskId)
    {
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid file.']);
        }
        $dir  = WRITEPATH . 'uploads/tasks/' . $taskId . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $name = $file->getRandomName();
        $file->move($dir, $name);
        $id = $this->attachments->insert([
            'task_id'   => $taskId,
            'user_id'   => session()->get('user_id'),
            'filename'  => $file->getClientName(),
            'filepath'  => 'uploads/tasks/' . $taskId . '/' . $name,
            'filesize'  => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
        return $this->response->setJSON(['success' => true, 'attachment' => $this->attachments->find($id)]);
    }

    // ── CHECKLIST TOGGLE ────────────────────────────────────────────────
    public function checklist(int $taskId)
    {
        $itemId = (int)$this->request->getPost('item_id');
        $action = $this->request->getPost('action'); // 'toggle' | 'add' | 'delete'

        if ($action === 'add') {
            $text = trim($this->request->getPost('text') ?? '');
            if (!$text) return $this->response->setJSON(['success' => false]);
            $id = $this->checklists->insert(['task_id' => $taskId, 'item_text' => $text]);
            return $this->response->setJSON(['success' => true, 'item' => $this->checklists->find($id)]);
        }

        if ($action === 'toggle') {
            $this->checklists->toggle($itemId, (int)session()->get('user_id'));
            return $this->response->setJSON(['success' => true]);
        }

        if ($action === 'delete') {
            $this->checklists->delete($itemId);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    // ── DELETE ───────────────────────────────────────────────────────────
    public function delete(int $id)
    {
        $this->tasks->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
