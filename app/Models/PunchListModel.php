<?php

namespace App\Models;

use CodeIgniter\Model;

class PunchListModel extends Model
{
    protected $table      = 'project_punch_list';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id', 'area_id', 'task_id', 'title', 'description', 
        'priority', 'assigned_to', 'status', 'due_date',
        'latitude', 'longitude', 'photo_path', 'created_by'
    ];

    public function forProject(int $projectId)
    {
        return $this->select('project_punch_list.*, fs_users.first_name, fs_users.last_name, project_areas.title as area_title')
            ->join('fs_users', 'fs_users.id = project_punch_list.assigned_to', 'left')
            ->join('project_areas', 'project_areas.id = project_punch_list.area_id', 'left')
            ->where('project_punch_list.project_id', $projectId)
            ->findAll();
    }
}
