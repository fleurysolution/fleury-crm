<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\ProjectModel;
use App\Models\PhaseModel;
use App\Models\ProjectMemberModel;

class Gantt extends BaseAppController
{
    /**
     * Render the Gantt tab inside the project workspace.
     * Delegates to projects/tabs/gantt_inline.php via Projects::show.
     */
    public function index(int $projectId): string
    {
        // Redirect to project workspace with gantt tab
        return redirect()->to(site_url("projects/{$projectId}?tab=gantt"));
    }

    /**
     * JSON API endpoint — returns task data formatted for Frappe Gantt.
     * GET /projects/:id/gantt/data
     */
    public function data(int $projectId): \CodeIgniter\HTTP\Response
    {
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);
        if (!$project) return $this->response->setJSON(['error' => 'Not found'])->setStatusCode(404);

        $taskModel = new TaskModel();
        $phases    = (new PhaseModel())->forProject($projectId);
        $tasks     = $taskModel->select('tasks.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS assignee_name')
            ->join('fs_users', 'fs_users.id = tasks.assigned_to', 'left')
            ->where('tasks.project_id', $projectId)
            ->where('tasks.deleted_at IS NULL')
            ->orderBy('tasks.phase_id')
            ->orderBy('tasks.sort_order')
            ->findAll();

        $db = \Config\Database::connect();
        $deps = $db->query('SELECT * FROM task_dependencies WHERE task_id IN (SELECT id FROM tasks WHERE project_id = ?)', [$projectId])->getResultArray();
        $depMap = [];
        foreach ($deps as $d) { $depMap[$d['task_id']][] = $d['depends_on_task_id']; }

        $phaseMap = [];
        foreach ($phases as $ph) { $phaseMap[$ph['id']] = $ph['title']; }

        $ganttTasks = [];

        // Add phase milestone rows as group headers
        foreach ($phases as $ph) {
            $ganttTasks[] = [
                'id'           => 'phase_' . $ph['id'],
                'name'         => $ph['title'],
                'start'        => null,
                'end'          => null,
                'progress'     => 0,
                'custom_class' => 'gantt-phase-bar',
                'is_group'     => true,
            ];
        }

        foreach ($tasks as $t) {
            $start = $t['start_date'] ?: date('Y-m-d');
            $end   = $t['due_date']   ?: date('Y-m-d', strtotime($start . ' +1 day'));
            // Frappe Gantt needs end > start
            if ($end <= $start) $end = date('Y-m-d', strtotime($start . ' +1 day'));

            $depIds = isset($depMap[$t['id']]) ? implode(',', $depMap[$t['id']]) : '';

            $ganttTasks[] = [
                'id'           => (string)$t['id'],
                'name'         => $t['title'],
                'start'        => $start,
                'end'          => $end,
                'progress'     => (int)$t['percent_complete'],
                'dependencies' => $depIds,
                'assignee'     => $t['assignee_name'] ?? '',
                'status'       => $t['status'],
                'priority'     => $t['priority'],
                'phase_id'     => $t['phase_id'],
                'phase_name'   => $phaseMap[$t['phase_id']] ?? '',
                'custom_class' => 'task-' . $t['status'],
            ];
        }

        return $this->response->setJSON(['success' => true, 'tasks' => $ganttTasks, 'project' => $project]);
    }

    /**
     * AJAX — update task start/end dates from Gantt drag.
     * POST /tasks/:id/gantt-update
     */
    public function updateDates(int $taskId): \CodeIgniter\HTTP\Response
    {
        $taskModel = new TaskModel();
        $task = $taskModel->find($taskId);
        if (!$task) return $this->response->setJSON(['success' => false, 'error' => 'Not found']);

        $data = [];
        if ($this->request->getPost('start_date')) $data['start_date'] = $this->request->getPost('start_date');
        if ($this->request->getPost('end_date'))   $data['due_date']   = $this->request->getPost('end_date');
        if ($this->request->getPost('progress'))   $data['percent_complete'] = (int)$this->request->getPost('progress');

        if (!empty($data)) $taskModel->update($taskId, $data);

        return $this->response->setJSON(['success' => true]);
    }
}
