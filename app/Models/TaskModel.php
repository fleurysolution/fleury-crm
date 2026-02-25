<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table          = 'tasks';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'project_id', 'phase_id', 'parent_task_id', 'milestone_id',
        'area_id', 'cost_code_id', 'title', 'description',
        'assigned_to', 'status', 'priority',
        'start_date', 'due_date', 'estimated_hours', 'actual_hours',
        'percent_complete', 'sort_order', 'created_by',
    ];

    public function withAssignee(): static
    {
        return $this
            ->select('tasks.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS assignee_name, fs_users.email AS assignee_email')
            ->join('fs_users', 'fs_users.id = tasks.assigned_to', 'left');
    }

    public function getForProject(int $projectId, ?string $status = null): array
    {
        $q = $this->withAssignee()
            ->where('tasks.project_id', $projectId)
            ->orderBy('tasks.phase_id')
            ->orderBy('tasks.sort_order');

        if ($status) {
            $q->where('tasks.status', $status);
        }
        return $q->findAll();
    }

    /** Group tasks by status for Kanban view */
    public function getKanbanColumns(int $projectId): array
    {
        $tasks = $this->getForProject($projectId);
        $cols  = ['todo' => [], 'in_progress' => [], 'review' => [], 'done' => [], 'blocked' => []];
        foreach ($tasks as $t) {
            $cols[$t['status']][] = $t;
        }
        return $cols;
    }
}
