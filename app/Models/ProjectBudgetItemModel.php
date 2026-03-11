<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectBudgetItemModel extends Model
{
    protected $table          = 'project_budget_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'project_id',
        'tenant_id',
        'cost_code_id',
        'title',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost'
    ];

    public function getForProject(int $projectId, ?int $tenantId): array
    {
        return $this->select('project_budget_items.*, cost_codes.code as cost_code, cost_codes.name as cost_code_name')
            ->join('cost_codes', 'cost_codes.id = project_budget_items.cost_code_id', 'left')
            ->where('project_budget_items.project_id', $projectId)
            ->where('project_budget_items.tenant_id', $tenantId)
            ->where('project_budget_items.deleted_at IS NULL')
            ->orderBy('cost_codes.code', 'ASC')
            ->findAll();
    }
}
