<?php

namespace App\Models;

use CodeIgniter\Model;

class RFIModel extends Model
{
    protected $table         = 'rfis';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'project_id','rfi_number','title','description','discipline','status','priority',
        'submitted_by','assigned_to','area_id','task_id','due_date','answered_at',
        'cost_impact','schedule_impact',
    ];

    public function forProject(int $projectId): array
    {
        $db = \Config\Database::connect();
        $query = $db->table('rfis')
            ->select('rfis.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS submitter_name, CONCAT(a.first_name, " ", a.last_name) AS assignee_name')
            ->join('fs_users AS a', 'a.id = rfis.assigned_to', 'left')
            ->join('fs_users', 'fs_users.id = rfis.submitted_by', 'left')
            ->where('rfis.project_id', $projectId)
            ->where('rfis.deleted_at IS NULL');
            
        // RBAC: If the user is a Subcontractor/Vendor, only show RFIs they're involved in
        $userId = session()->get('user_id');
        $roleSlug = session()->get('role_slug') ?? 'employee';
        
        if ($roleSlug === 'subcontractor_vendor') {
            $query->groupStart()
                  ->where('rfis.submitted_by', $userId)
                  ->orWhere('rfis.assigned_to', $userId)
                  ->groupEnd();
        }

        return $query->orderBy('rfis.id', 'DESC')->get()->getResultArray();
    }

    public function nextNumber(int $projectId): string
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COUNT(*)+1 AS n FROM rfis WHERE project_id = ?', [$projectId])->getRow();
        return 'RFI-' . str_pad($row->n, 4, '0', STR_PAD_LEFT);
    }

    public function statusCounts(int $projectId): array
    {
        $rows = $this->selectCount('rfis.id', 'cnt')->select('rfis.status')
            ->where('rfis.project_id', $projectId)->where('rfis.deleted_at IS NULL')
            ->groupBy('rfis.status')->findAll();
        $out = [];
        foreach ($rows as $r) { $out[$r['status']] = (int)$r['cnt']; }
        return $out;
    }
}
