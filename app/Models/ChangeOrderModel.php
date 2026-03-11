<?php

namespace App\Models;

use CodeIgniter\Model;

class ChangeOrderModel extends Model
{
    protected $table          = 'change_orders';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = [
        'tenant_id', 'project_id', 'event_id', 'co_number', 
        'title', 'description', 'amount', 'status', 'approved_at'
    ];
    protected $useTimestamps   = true;

    public function getForProject(int $projectId, ?int $tenantId)
    {
        return $this->where('project_id', $projectId)
                    ->where('tenant_id', $tenantId)
                    ->findAll();
    }
}
