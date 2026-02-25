<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteDiaryModel extends Model
{
    protected $table          = 'site_diary_entries';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','entry_date','weather','temperature','manpower_count',
        'working_hours','notes','status','created_by','approved_by','approved_at',
    ];

    public function forProject(int $projectId, int $limit = 30): array
    {
        return $this->select('site_diary_entries.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = site_diary_entries.created_by', 'left')
            ->where('site_diary_entries.project_id', $projectId)
            ->where('site_diary_entries.deleted_at IS NULL')
            ->orderBy('entry_date', 'DESC')
            ->findAll($limit);
    }

    public function forDate(int $projectId, string $date): ?array
    {
        return $this->where('project_id', $projectId)->where('entry_date', $date)->first();
    }
}
