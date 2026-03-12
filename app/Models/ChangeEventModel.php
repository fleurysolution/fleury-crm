<?php

namespace App\Models;

use CodeIgniter\Model;

class ChangeEventModel extends ErpModel
{
    protected $table          = 'change_events';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';

    protected $enforceBranchLinkage = false;

    protected $allowedFields   = [
        'tenant_id', 'project_id', 'title', 'description', 
        'status', 'type', 'estimated_cost', 'created_at', 'updated_at'
    ];
    protected $useTimestamps   = true;

    public function getForProject(int $projectId, ?int $tenantId)
    {
        return $this->where('project_id', $projectId)
                    ->where('tenant_id', $tenantId)
                    ->findAll();
    }
}
