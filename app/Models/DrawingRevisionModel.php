<?php

namespace App\Models;

use CodeIgniter\Model;

class DrawingRevisionModel extends Model
{
    protected $table          = 'project_drawing_revisions';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'drawing_id',
        'revision_no',
        'revision_date',
        'filepath',
        'notes',
        'uploaded_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all revisions for a specific drawing.
     */
    public function forDrawing(int $drawingId): array
    {
        return $this->select('project_drawing_revisions.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS uploader_name')
            ->join('fs_users', 'fs_users.id = project_drawing_revisions.uploaded_by', 'left')
            ->where('project_drawing_revisions.drawing_id', $drawingId)
            ->where('project_drawing_revisions.deleted_at IS NULL')
            ->orderBy('project_drawing_revisions.created_at', 'DESC')
            ->findAll();
    }
}
