<?php

namespace App\Models;

class SubmittalModel extends ErpModel
{
    protected $table = 'fs_submittals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'tenant_id',
        'branch_id',
        'submittal_number',
        'type',
        'spec_section',
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
        'revision',
        'created_by',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function forProject(int $projectId)
    {
        return $this->select($this->table . '.*, 
                            CONCAT(u1.first_name, " ", u1.last_name) AS submitter_name,
                            CONCAT(u2.first_name, " ", u2.last_name) AS reviewer_name')
                    ->join('fs_users u1', 'u1.id = ' . $this->table . '.created_by', 'left')
                    ->join('fs_users u2', 'u2.id = ' . $this->table . '.assigned_to', 'left')
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
            'draft'              => 0,
            'submitted'          => 0,
            'under_review'       => 0,
            'approved'           => 0,
            'approved_as_noted'  => 0,
            'rejected'           => 0,
            'resubmit'           => 0,
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }

        return $counts;
    }
}
