<?php

namespace App\Models;

use App\Models\ErpModel;

class ProjectModel extends ErpModel
{
    protected $table          = 'projects';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'title', 'tenant_id', 'branch_id', 'client_id', 'pm_user_id', 'status', 'project_stage', 'priority',
        'start_date', 'end_date', 'budget', 'contract_type', 'versioned_budget_baseline', 'currency', 'description',
        'region_id', 'office_id', 'color', 'created_by',
        'latitude', 'longitude', 'geofence_radius'
    ];

    /** Return projects with client name + PM name joined */
    public function withDetails(): static
    {
        return $this
            ->select('projects.*, clients.company_name AS client_name, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS pm_name')
            ->join('clients', 'clients.id = projects.client_id', 'left')
            ->join('fs_users', 'fs_users.id = projects.pm_user_id', 'left');
    }

    public function getByStatus(string $status): array
    {
        return $this->withDetails()->where('projects.status', $status)->get()->getResultArray();
    }

    /** Task completion stats for a project */
    public function getStats(int $projectId): array
    {
        $db   = \Config\Database::connect();
        $row  = $db->query("
            SELECT
                COUNT(*) AS total,
                SUM(status = 'done') AS done,
                SUM(status = 'in_progress') AS in_progress,
                SUM(status = 'blocked') AS blocked
            FROM tasks
            WHERE project_id = ? AND deleted_at IS NULL
        ", [$projectId])->getRowArray();

        $total = (int)($row['total'] ?? 0);
        return [
            'total'        => $total,
            'done'         => (int)($row['done'] ?? 0),
            'in_progress'  => (int)($row['in_progress'] ?? 0),
            'blocked'      => (int)($row['blocked'] ?? 0),
            'percent'      => $total > 0 ? round(($row['done'] / $total) * 100) : 0,
        ];
    }
}
