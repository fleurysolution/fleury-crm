<?php namespace App\Models;
use CodeIgniter\Model;
class AreaModel extends Model {
    protected $table          = 'areas';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = ['project_id','parent_id','name','type','status','start_date','end_date','turnover_date','description','sort_order'];

    /** Return entire area tree for a project as nested array */
    public function getTree(int $projectId): array {
        $all   = $this->where('project_id', $projectId)->orderBy('sort_order')->findAll();
        $index = [];
        foreach ($all as &$a) { $a['children'] = []; $index[$a['id']] = &$a; }
        $tree  = [];
        foreach ($index as &$a) {
            if ($a['parent_id'] && isset($index[$a['parent_id']])) {
                $index[$a['parent_id']]['children'][] = &$a;
            } else {
                $tree[] = &$a;
            }
        }
        return $tree;
    }
}
