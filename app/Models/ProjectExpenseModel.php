<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectExpenseModel extends Model
{
    protected $table          = 'project_expenses';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','cost_code_id','category','description','amount','currency',
        'expense_date','vendor','receipt_path','status','submitted_by','approved_by','approved_at',
    ];

    public function forProject(int $projectId, string $status = ''): array
    {
        $q = $this->select('project_expenses.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS submitter_name, CONCAT(a.first_name, " ", a.last_name) AS approver_name')
            ->join('fs_users', 'fs_users.id = project_expenses.submitted_by', 'left')
            ->join('fs_users AS a', 'a.id = project_expenses.approved_by', 'left')
            ->where('project_expenses.project_id', $projectId)
            ->where('project_expenses.deleted_at IS NULL');
        if ($status) $q = $q->where('project_expenses.status', $status);
        return $q->orderBy('expense_date', 'DESC')->findAll();
    }

    public function totalApproved(int $projectId): float
    {
        $db = \Config\Database::connect();
        $r  = $db->query(
            'SELECT COALESCE(SUM(amount),0) AS t FROM project_expenses WHERE project_id=? AND status="approved" AND deleted_at IS NULL',
            [$projectId]
        )->getRow();
        return (float)($r->t ?? 0);
    }

    public function totalByCategory(int $projectId): array
    {
        $db = \Config\Database::connect();
        return $db->query(
            'SELECT category, COALESCE(SUM(amount),0) AS total FROM project_expenses WHERE project_id=? AND status="approved" AND deleted_at IS NULL GROUP BY category ORDER BY total DESC',
            [$projectId]
        )->getResultArray();
    }
}
