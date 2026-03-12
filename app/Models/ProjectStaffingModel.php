<?php namespace App\Models;

use CodeIgniter\Model;

class ProjectStaffingModel extends Model
{
    protected $table          = 'project_staffing_plan';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['project_id', 'role_or_trade', 'planned_count', 'start_date', 'end_date', 'description'];

    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->where('deleted_at IS NULL')
            ->findAll();
    }
}
