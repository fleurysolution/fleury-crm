<?php

namespace App\Models;

class TaskModel extends ErpModel
{
    protected $table          = 'tasks';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'tenant_id', 'branch_id', 'project_id', 'phase_id', 'parent_task_id', 'milestone_id',
        'area_id', 'cost_code_id', 'title', 'description',
        'assigned_to', 'status', 'priority',
        'start_date', 'start_time', 'due_date', 'end_time', 
        'estimated_hours', 'actual_hours', 'points', 'labels', 'recurring_rule',
        'percent_complete', 'sort_order', 'created_by',
        'activity_id', 'is_milestone', 'constraint_type', 'constraint_date',
        'original_duration', 'early_start', 'early_finish', 'late_start', 'late_finish',
        'total_float', 'is_critical'
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
