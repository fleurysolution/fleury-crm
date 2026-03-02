<?php

namespace App\Models;

use CodeIgniter\Model;

class DrawingModel extends Model
{
    protected $table          = 'project_drawings';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'drawing_no',
        'title',
        'discipline',
        'status',
        'current_revision',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all master drawings for a project, optionally mapped with uploader name.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_drawings.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = project_drawings.created_by', 'left')
            ->where('project_drawings.project_id', $projectId)
            ->where('project_drawings.deleted_at IS NULL')
            ->orderBy('project_drawings.discipline', 'ASC')
            ->orderBy('project_drawings.drawing_no', 'ASC')
            ->findAll();
    }
}
