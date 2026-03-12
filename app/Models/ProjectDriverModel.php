<?php namespace App\Models;

use CodeIgniter\Model;

class ProjectDriverModel extends Model
{
    protected $table          = 'project_drivers';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['project_id', 'name', 'unit', 'value', 'description'];

    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->where('deleted_at IS NULL')
            ->findAll();
    }
}
