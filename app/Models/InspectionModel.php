<?php

namespace App\Models;

class InspectionModel extends ErpModel
{
    protected $table          = 'fs_inspections';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id', 'tenant_id', 'branch_id', 'type', 'status', 'inspector_id', 
        'inspection_date', 'notes', 'created_by'
    ];

    public function forProject(int $projectId): array
    {
        $db = \Config\Database::connect();
        $q = $db->table('fs_inspections')
            ->select('fs_inspections.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS inspector_name, CONCAT(creator.first_name, " ", creator.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = fs_inspections.inspector_id', 'left')
            ->join('fs_users AS creator', 'creator.id = fs_inspections.created_by', 'left')
            ->where('fs_inspections.project_id', $projectId)
            ->where('fs_inspections.deleted_at IS NULL');
            
        return $q->orderBy('fs_inspections.id', 'DESC')->get()->getResultArray();
    }
}
