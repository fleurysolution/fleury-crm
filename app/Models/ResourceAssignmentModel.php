<?php

namespace App\Models;

use CodeIgniter\Model;

class ResourceAssignmentModel extends Model
{
    protected $table         = 'resource_assignments';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['task_id','user_id','planned_hours','role'];

    /**
     * All assignments for a task with assignee name.
     */
    public function forTask(int $taskId): array
    {
        return $this->select('resource_assignments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS user_name')
                    ->join('fs_users', 'fs_users.id = resource_assignments.user_id', 'left')
                    ->where('resource_assignments.task_id', $taskId)
                    ->findAll();
    }

    /**
     * All assignments for a project.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('resource_assignments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS user_name, tasks.title AS task_title')
                    ->join('tasks',    'tasks.id = resource_assignments.task_id', 'inner')
                    ->join('fs_users', 'fs_users.id = resource_assignments.user_id', 'left')
                    ->where('tasks.project_id', $projectId)
                    ->orderBy('fs_users.first_name', 'ASC')
                    ->orderBy('fs_users.last_name', 'ASC')
                    ->findAll();
    }
}
