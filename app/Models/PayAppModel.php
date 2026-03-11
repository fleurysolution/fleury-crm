<?php

namespace App\Models;

use CodeIgniter\Model;

class PayAppModel extends ErpModel
{
    protected $table          = 'project_pay_apps';
    protected $primaryKey     = 'id';
    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'project_id',
        'application_no',
        'period_to',
        'status',
        'retainage_percentage',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all Pay Apps for a specific project.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_pay_apps.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = project_pay_apps.created_by', 'left')
            ->where('project_pay_apps.project_id', $projectId)
            ->orderBy('project_pay_apps.application_no', 'ASC')
            ->findAll();
    }
}
