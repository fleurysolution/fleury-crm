<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table          = 'project_estimates';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'title',
        'status',
        'total_amount',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all estimates for a specific project.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_estimates.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = project_estimates.created_by', 'left')
            ->where('project_estimates.project_id', $projectId)
            ->where('project_estimates.deleted_at IS NULL')
            ->orderBy('project_estimates.created_at', 'DESC')
            ->findAll();
    }
}
