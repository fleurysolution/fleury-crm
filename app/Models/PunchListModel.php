<?php

namespace App\Models;

use CodeIgniter\Model;

class PunchListModel extends Model
{
    protected $table          = 'project_punch_lists';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'item_no',
        'location',
        'description',
        'assigned_to',
        'status',
        'due_date',
        'resolved_at',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all punch list items for a specific project, including the assigned user's name.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_punch_lists.*, CONCAT(assignee.first_name, " ", assignee.last_name) AS assignee_name, CONCAT(creator.first_name, " ", creator.last_name) AS creator_name')
            ->join('fs_users assignee', 'assignee.id = project_punch_lists.assigned_to', 'left')
            ->join('fs_users creator', 'creator.id = project_punch_lists.created_by', 'left')
            ->where('project_punch_lists.project_id', $projectId)
            ->orderBy('project_punch_lists.status', 'ASC') // Open first
            ->orderBy('project_punch_lists.due_date', 'ASC')
            ->findAll();
    }
}
