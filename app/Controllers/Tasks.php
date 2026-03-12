<?php

namespace App\Controllers;

use App\Controllers\BaseAppController;
use App\Models\TaskModel;
use App\Models\TaskCommentModel;
use App\Models\TaskAttachmentModel;
use App\Models\TaskChecklistModel;
use App\Models\PhaseModel;
use App\Models\TaskCollaboratorModel;
use App\Models\ProjectModel;
use App\Models\UserModel;

class Tasks extends BaseAppController
{
    protected TaskModel           $tasks;
    protected TaskAttachmentModel $attachments;
    protected TaskChecklistModel  $checklists;
    protected \App\Models\TaskCollaboratorModel $collaborators;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->tasks       = new TaskModel();
        $this->comments    = new TaskCommentModel();
        $this->attachments = new TaskAttachmentModel();
        $this->checklists  = new TaskChecklistModel();
        $this->collaborators = new \App\Models\TaskCollaboratorModel();
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
        $project = (new ProjectModel())->find($projectId);
        
        $data = [
            'tenant_id'   => $project['tenant_id'],
            'branch_id'   => $project['branch_id'],
            'project_id'  => $projectId,
            'phase_id'    => $this->request->getPost('phase_id') ?: null,
            'milestone_id'=> $this->request->getPost('milestone_id') ?: null,
            'title'       => trim($this->request->getPost('title')),
            'description' => $this->request->getPost('description') ?: null,
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'status'      => $this->request->getPost('status') ?? 'todo',
            'priority'    => $this->request->getPost('priority') ?? 'medium',
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'start_time'  => $this->request->getPost('start_time') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'end_time'    => $this->request->getPost('end_time') ?: null,
            'estimated_hours' => $this->request->getPost('estimated_hours') ?: null,
            'points'      => (int)$this->request->getPost('points'),
            'labels'      => $this->request->getPost('labels') ?: null,
            'recurring_rule' => $this->request->getPost('recurring_rule') ?: null,
            'created_by'  => session()->get('user_id'),
        ];

        if (empty($data['title'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Title is required.']);
        }

        $id = $this->tasks->insert($data);

        // Sync Collaborators
        $collabs = $this->request->getPost('collaborators');
        if (is_array($collabs)) {
            $this->collaborators->syncCollaborators($id, $collabs);
        }

        $task = $this->tasks->withAssignee()->where('tasks.id', $id)->first();
        return $this->response->setJSON(['success' => true, 'task' => $task]);
    }

    // ── SHOW (AJAX detail panel) ────────────────────────────────────────
    public function show(int $id)
    {
        $task = $this->tasks->withAssignee()->where('tasks.id', $id)->first();
        if (!$task) return $this->response->setJSON(['success' => false]);
        
        $comments     = $this->comments->forTask($id);
        $attachments  = $this->attachments->forTask($id);
        $checklists   = $this->checklists->forTask($id);
        $taskCollabs  = $this->collaborators->getCollaborators($id);
        $qaChecklists = (new \App\Models\QaChecklistModel())->forTask($id);
        
        $users = model(UserModel::class)->select('id, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS name, email')->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'       => true,
                'task'          => $task,
                'comments'      => $comments,
                'attachments'   => $attachments,
                'checklists'    => $checklists,
                'qa_checklists' => $qaChecklists,
                'collaborators' => $taskCollabs,
            ]);
        }
        // If falling back to full view:
        return view('projects/partials/task_modal', compact('task','comments','attachments','checklists','taskCollabs','users'));
    }

    // ── UPDATE (AJAX) ────────────────────────────────────────────────────
    public function update(int $id)
    {
        $allowed = ['title','description','assigned_to','status','priority',
                    'start_date','start_time','due_date','end_time','estimated_hours','percent_complete',
                    'phase_id','milestone_id','area_id','cost_code_id', 'points', 'labels', 'recurring_rule'];
        $data = [];
        foreach ($allowed as $f) {
            $v = $this->request->getPost($f);
            if ($v !== null) { $data[$f] = $v === '' ? null : $v; }
        }
        if (!empty($data)) {
            $this->tasks->update($id, $data);
        }

        // Sync Collaborators
        $collabs = $this->request->getPost('collaborators');
        if ($collabs !== null) {
            // Can be empty array to clear all
            $colArr = is_array($collabs) ? $collabs : [];
            $this->collaborators->syncCollaborators($id, $colArr);
        }

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

    public function comment(int $taskId)
    {
        $body = trim($this->request->getPost('body') ?? '');
        if (!$body && !$this->request->getFile('attachment')) return $this->response->setJSON(['success' => false]);
        
        $task = $this->tasks->find($taskId);
        $data = [
            'tenant_id' => $task['tenant_id'],
            'branch_id' => $task['branch_id'],
            'task_id'   => $taskId,
            'user_id'   => session()->get('user_id'),
            'body'      => $body,
        ];

        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid()) {
            $dir = FCPATH . 'uploads/tasks/' . $taskId . '/comments/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $name = $file->getRandomName();
            $file->move($dir, $name);
            $data['attachment_name'] = $file->getClientName();
            $data['attachment_path'] = 'uploads/tasks/' . $taskId . '/comments/' . $name;
        }

        $id  = $this->comments->insert($data);
        $c = $this->comments->select('task_comments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name')
            ->join('fs_users','fs_users.id = task_comments.user_id','left')
            ->find($id);
        return $this->response->setJSON(['success' => true, 'comment' => $c]);
    }

    // ── UPLOAD ───────────────────────────────────────────────────────────
    public function upload(int $taskId)
    {
        $files = $this->request->getFiles();
        if (!$files || empty($files['files'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'No files uploaded.']);
        }

        $task = $this->tasks->find($taskId);
        $dir  = FCPATH . 'uploads/tasks/' . $taskId . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $attachments = [];
        $uploadFiles = is_array($files['files']) ? $files['files'] : [$files['files']];
        
        foreach ($uploadFiles as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $name = $file->getRandomName();
                $file->move($dir, $name);
                
                $id = $this->attachments->insert([
                    'tenant_id' => $task['tenant_id'],
                    'branch_id' => $task['branch_id'],
                    'task_id'   => $taskId,
                    'user_id'   => session()->get('user_id'),
                    'filename'  => $file->getClientName(),
                    'filepath'  => 'uploads/tasks/' . $taskId . '/' . $name,
                    'filesize'  => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
                $attachments[] = $this->attachments->find($id);
            }
        }

        if (empty($attachments)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to process files.']);
        }
        
        return $this->response->setJSON(['success' => true, 'attachments' => $attachments]);
    }

    // ── CHECKLIST TOGGLE ────────────────────────────────────────────────
    public function checklist(int $taskId)
    {
        $itemId = (int)$this->request->getPost('item_id');
        $action = $this->request->getPost('action'); // 'toggle' | 'add' | 'delete'

        if ($action === 'add') {
            $text = trim($this->request->getPost('text') ?? '');
            if (!$text) return $this->response->setJSON(['success' => false]);
            $task = $this->tasks->find($taskId);
            $id = $this->checklists->insert([
                'tenant_id' => $task['tenant_id'],
                'branch_id' => $task['branch_id'],
                'task_id'   => $taskId, 
                'item_text' => $text
            ]);
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

    // ── QA CHECKLIST TOGGLE ─────────────────────────────────────────────
    public function qaToggle(int $taskId)
    {
        $itemId = (int)$this->request->getPost('item_id');
        $model  = new \App\Models\QaChecklistModel();
        $item   = $model->find($itemId);
        
        if ($item && $item['task_id'] == $taskId) {
            $model->update($itemId, [
                'passed'       => $item['passed'] ? 0 : 1,
                'inspected_by' => session()->get('user_id'),
                'inspected_at' => date('Y-m-d H:i:s')
            ]);
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
