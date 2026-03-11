<?php

namespace App\Models;

use CodeIgniter\Model;

class ChangeEventModel extends Model
{
    protected $table          = 'change_events';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = [
        'tenant_id', 'project_id', 'title', 'description', 
        'status', 'type', 'estimated_cost'
    ];
    protected $useTimestamps   = true;

    public function getForProject(int $projectId, ?int $tenantId)
    {
        return $this->where('project_id', $projectId)
                    ->where('tenant_id', $tenantId)
                    ->findAll();
    }
}
