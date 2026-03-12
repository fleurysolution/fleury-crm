<?php

namespace App\Models;

use CodeIgniter\Model;

class BOQItemModel extends Model
{
    protected $table          = 'boq_items';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','contract_id','cost_code_id','parent_id','item_code',
        'description','unit','quantity','unit_rate','total_amount',
        'actual_qty','actual_amount','is_section','sort_order',
        'driver_id', 'driver_multiplier'
    ];

    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->where('deleted_at IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->findAll();
    }

    public function totalBOQ(int $projectId): float
    {
        $db = \Config\Database::connect();
        $r  = $db->query(
            'SELECT COALESCE(SUM(total_amount),0) AS t FROM boq_items WHERE project_id=? AND is_section=0 AND deleted_at IS NULL',
            [$projectId]
        )->getRow();
        return (float)($r->t ?? 0);
    }

    public function totalActual(int $projectId): float
    {
        $db = \Config\Database::connect();
        $r  = $db->query(
            'SELECT COALESCE(SUM(actual_amount),0) AS t FROM boq_items WHERE project_id=? AND is_section=0 AND deleted_at IS NULL',
            [$projectId]
        )->getRow();
        return (float)($r->t ?? 0);
    }

    public function buildTree(int $projectId): array
    {
        $all     = $this->forProject($projectId);
        $indexed = [];
        $tree    = [];
        foreach ($all as $item) {
            $item['children'] = [];
            $indexed[$item['id']] = $item;
        }
        foreach ($indexed as $id => &$item) {
            if ($item['parent_id'] && isset($indexed[$item['parent_id']])) {
                $indexed[$item['parent_id']]['children'][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }
        return $tree;
    }
}
