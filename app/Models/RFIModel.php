<?php

namespace App\Models;

class RfiModel extends ErpModel
{
    protected $table = 'fs_rfis';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'tenant_id',
        'branch_id',
        'rfi_number',
        'title',
        'description',
        'proposed_solution',
        'discipline',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'area_id',
        'task_id',
        'created_by',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function forProject(int $projectId)
    {
        return $this->select($this->table . '.*, CONCAT(u.first_name, " ", u.last_name) AS assignee_name')
                    ->join('fs_users u', 'u.id = ' . $this->table . '.assigned_to', 'left')
                    ->where($this->table . '.project_id', $projectId)
                    ->orderBy($this->table . '.created_at', 'DESC')
                    ->findAll();
    }

    public function statusCounts(int $projectId): array
    {
        $results = $this->select('status, COUNT(*) as count')
                        ->where('project_id', $projectId)
                        ->groupBy('status')
                        ->findAll();
        
        $counts = [
            'draft'        => 0,
            'submitted'    => 0,
            'under_review' => 0,
            'answered'     => 0,
            'closed'       => 0,
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }

        return $counts;
    }
}
