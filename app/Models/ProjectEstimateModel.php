<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectEstimateModel extends ErpModel
{
    protected $table          = 'project_estimates';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'project_id',
        'title',
        'status',
        'total_amount',
        'risk_summary',
        'clarifications',
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

    /**
     * Get all estimates across all projects for the global index.
     */
    public function getAllWithProjects(): array
    {
        return $this->select('project_estimates.*, projects.title AS project_title, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('projects', 'projects.id = project_estimates.project_id', 'left')
            ->join('fs_users', 'fs_users.id = project_estimates.created_by', 'left')
            ->orderBy('project_estimates.created_at', 'DESC')
            ->findAll();
    }
}
