<?php

namespace App\Models;

class PunchListItemModel extends ErpModel
{
    protected $table          = 'punch_list_items';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','tenant_id','branch_id','area_id','task_id','item_number','title','description',
        'trade','status','priority','reported_by','assigned_to','due_date',
        'resolved_at','closed_at','latitude','longitude','photo_path'
    ];

    public function forProject(int $projectId, string $status = ''): array
    {
        $db = \Config\Database::connect();
        $q = $db->table('punch_list_items')
            ->select('punch_list_items.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS assignee_name, CONCAT(r.first_name, " ", r.last_name) AS reporter_name, areas.name AS area_name')
            ->join('fs_users', 'fs_users.id = punch_list_items.assigned_to', 'left')
            ->join('fs_users AS r', 'r.id = punch_list_items.reported_by', 'left')
            ->join('areas', 'areas.id = punch_list_items.area_id', 'left')
            ->where('punch_list_items.project_id', $projectId)
            ->where('punch_list_items.deleted_at IS NULL');
        if ($status) $q = $q->where('punch_list_items.status', $status);
        
        // RBAC: If the user is a Subcontractor/Vendor, only show items they're involved in
        $userId = session()->get('user_id');
        $roleSlug = session()->get('role_slug') ?? 'employee';
        
        if ($roleSlug === 'subcontractor_vendor') {
            $q->groupStart()
              ->where('punch_list_items.reported_by', $userId)
              ->orWhere('punch_list_items.assigned_to', $userId)
              ->groupEnd();
        }

        return $q->orderBy('punch_list_items.id', 'DESC')->get()->getResultArray();
    }

    public function nextNumber(int $projectId): string
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COUNT(*)+1 AS n FROM punch_list_items WHERE project_id = ?', [$projectId])->getRow();
        return 'PL-' . str_pad($row->n, 4, '0', STR_PAD_LEFT);
    }

    public function statusCounts(int $projectId): array
    {
        $rows = $this->selectCount('punch_list_items.id','cnt')->select('punch_list_items.status')
            ->where('punch_list_items.project_id', $projectId)->where('punch_list_items.deleted_at IS NULL')
            ->groupBy('punch_list_items.status')->findAll();
        $out = [];
        foreach ($rows as $r) { $out[$r['status']] = (int)$r['cnt']; }
        return $out;
    }

    public function agingCounts(int $projectId): array
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT
                SUM(CASE WHEN DATEDIFF(NOW(), created_at) <= 7  THEN 1 ELSE 0 END) AS week0,
                SUM(CASE WHEN DATEDIFF(NOW(), created_at) BETWEEN 8  AND 14 THEN 1 ELSE 0 END) AS week1,
                SUM(CASE WHEN DATEDIFF(NOW(), created_at) BETWEEN 15 AND 21 THEN 1 ELSE 0 END) AS week2,
                SUM(CASE WHEN DATEDIFF(NOW(), created_at) > 21 THEN 1 ELSE 0 END) AS older
            FROM punch_list_items
            WHERE project_id = ? AND status IN ('open','in_progress') AND deleted_at IS NULL
        ", [$projectId])->getRowArray();
    }
}
