<?php

namespace App\Models;

class DrawingModel extends ErpModel
{
    protected $table = 'fs_drawings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'tenant_id',
        'branch_id',
        'discipline',
        'drawing_number',
        'title',
        'current_revision_id',
        'revision',
        'file_path',
        'status',
        'created_by',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function forProject(int $projectId)
    {
        return $this->select($this->table . '.*, r.revision_no AS revision, CONCAT(u.first_name, " ", u.last_name) AS creator_name')
                    ->join('project_drawing_revisions r', 'r.id = ' . $this->table . '.current_revision_id', 'left')
                    ->join('fs_users u', 'u.id = r.uploaded_by', 'left')
                    ->where($this->table . '.project_id', $projectId)
                    ->orderBy($this->table . '.drawing_number', 'ASC')
                    ->findAll();
    }
}
