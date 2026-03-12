<?php

namespace App\Models;

use CodeIgniter\Model;

class QaChecklistModel extends Model
{
    protected $table      = 'project_qa_checklists';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id', 'task_id', 'title', 'category', 'is_mandatory', 
        'requires_photo', 'passed', 'inspected_by', 'inspected_at', 'notes'
    ];

    public function forTask(int $taskId)
    {
        return $this->where('task_id', $taskId)->findAll();
    }
}
