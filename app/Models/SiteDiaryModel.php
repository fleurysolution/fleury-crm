<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteDiaryModel extends Model
{
    protected $table          = 'project_site_diaries';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'report_date',
        'weather_conditions',
        'temperature',
        'work_performed',
        'materials_received',
        'safety_observations',
        'status',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all daily logs for a specific project.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_site_diaries.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = project_site_diaries.created_by', 'left')
            ->where('project_site_diaries.project_id', $projectId)
            ->orderBy('project_site_diaries.report_date', 'DESC')
            ->findAll();
    }
}
