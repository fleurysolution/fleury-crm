<?php

namespace App\Models;

use CodeIgniter\Model;

class BidPackageModel extends Model
{
    protected $table          = 'bid_packages';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['tenant_id', 'project_id', 'title', 'description', 'due_date', 'status'];
    protected $useTimestamps   = true;

    public function getForProject(int $projectId, ?int $tenantId)
    {
        return $this->where('project_id', $projectId)
                    ->where('tenant_id', $tenantId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
