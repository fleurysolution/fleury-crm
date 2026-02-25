<?php namespace App\Models;
use CodeIgniter\Model;
class MilestoneModel extends Model {
    protected $table         = 'project_milestones';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['project_id','title','description','due_date','status','is_client_facing','acceptance_criteria','created_by'];
    public function forProject(int $id): array {
        return $this->where('project_id', $id)->orderBy('due_date')->findAll();
    }
}
