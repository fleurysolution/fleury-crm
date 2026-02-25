<?php namespace App\Models;
use CodeIgniter\Model;
class PhaseModel extends Model {
    protected $table         = 'wbs_phases';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['project_id','title','color','sort_order'];
    public function forProject(int $id): array {
        return $this->where('project_id', $id)->orderBy('sort_order')->findAll();
    }
}
