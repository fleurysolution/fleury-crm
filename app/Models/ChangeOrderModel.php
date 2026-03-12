<?php

namespace App\Models;

use CodeIgniter\Model;

class ChangeOrderModel extends ErpModel
{
    protected $table          = 'change_orders';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';

    protected $enforceBranchLinkage = false;

    protected $allowedFields   = [
        'tenant_id', 'project_id', 'event_id', 'contract_id', 'co_number', 
        'title', 'description', 'amount', 'status', 'approved_at', 
        'created_at', 'updated_at'
    ];
    protected $useTimestamps   = true;

    public function getForProject(int $projectId, ?int $tenantId)
    {
        return $this->where('project_id', $projectId)
                    ->where('tenant_id', $tenantId)
                    ->findAll();
    }
}
