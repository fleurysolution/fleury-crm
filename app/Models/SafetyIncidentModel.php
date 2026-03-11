<?php

namespace App\Models;

class SafetyIncidentModel extends ErpModel
{
    protected $table          = 'fs_safety_incidents';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id', 'tenant_id', 'branch_id', 'incident_date', 'type', 
        'severity', 'description', 'reported_by', 'status', 'created_by'
    ];

    public function forProject(int $projectId): array
    {
        $db = \Config\Database::connect();
        $q = $db->table('fs_safety_incidents')
            ->select('fs_safety_incidents.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS reporter_name')
            ->join('fs_users', 'fs_users.id = fs_safety_incidents.reported_by', 'left')
            ->where('fs_safety_incidents.project_id', $projectId)
            ->where('fs_safety_incidents.deleted_at IS NULL');
            
        return $q->orderBy('fs_safety_incidents.incident_date', 'DESC')->get()->getResultArray();
    }
}
