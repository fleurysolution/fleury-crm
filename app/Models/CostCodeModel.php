<?php namespace App\Models;
use CodeIgniter\Model;
class CostCodeModel extends Model {
    protected $table          = 'cost_codes';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = ['project_id','parent_id','code','name','category'];
    /** Global codes + project-specific codes merged */
    public function forProject(?int $projectId): array {
        return $this->groupStart()
            ->where('project_id', null)
            ->orWhere('project_id', $projectId)
            ->groupEnd()
            ->orderBy('code')
            ->findAll();
    }
}
