<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmittalModel extends Model
{
    protected $table          = 'submittals';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','submittal_number','title','spec_section','type','status',
        'submitted_by','reviewer_id','due_date','current_revision','days_in_review',
    ];

    public function forProject(int $projectId): array
    {
        $db = \Config\Database::connect();
        $query = $db->table('submittals')
            ->select('submittals.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS submitter_name, CONCAT(r.first_name, " ", r.last_name) AS reviewer_name')
            ->join('fs_users', 'fs_users.id = submittals.submitted_by', 'left')
            ->join('fs_users AS r', 'r.id = submittals.reviewer_id', 'left')
            ->where('submittals.project_id', $projectId)
            ->where('submittals.deleted_at IS NULL');
            
        // RBAC: If the user is a Subcontractor/Vendor, only show submittals they're involved in
        $userId = session()->get('user_id');
        $roleSlug = session()->get('role_slug') ?? 'employee';
        
        if ($roleSlug === 'subcontractor_vendor') {
            $query->groupStart()
                  ->where('submittals.submitted_by', $userId)
                  ->orWhere('submittals.reviewer_id', $userId)
                  ->groupEnd();
        }

        return $query->orderBy('submittals.submittal_number')->get()->getResultArray();
    }

    public function nextNumber(int $projectId): string
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COUNT(*)+1 AS n FROM submittals WHERE project_id = ?', [$projectId])->getRow();
        return 'SUB-' . str_pad($row->n, 4, '0', STR_PAD_LEFT);
    }

    public function statusCounts(int $projectId): array
    {
        $rows = $this->selectCount('submittals.id','cnt')->select('submittals.status')
            ->where('submittals.project_id', $projectId)->where('submittals.deleted_at IS NULL')
            ->groupBy('submittals.status')->findAll();
        $out = [];
        foreach ($rows as $r) { $out[$r['status']] = (int)$r['cnt']; }
        return $out;
    }

    public function withUserName()
    {
        $this->select('submittals.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS submitter_name, CONCAT(r.first_name, " ", r.last_name) AS reviewer_name');
        $this->join('fs_users', 'fs_users.id = submittals.submitted_by', 'left');
        $this->join('fs_users AS r', 'r.id = submittals.reviewer_id', 'left');
        return $this;
    }
}
