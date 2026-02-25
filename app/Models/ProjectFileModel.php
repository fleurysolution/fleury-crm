<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectFileModel extends Model
{
    protected $table         = 'project_files';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'project_id','entity_type','entity_id','name','stored_name',
        'path','mime_type','size','description','uploaded_by','created_at','deleted_at',
    ];

    // ── Query helpers ───────────────────────────────────────────────────────

    /** All files for a project (not deleted), with uploader name */
    public function forProject(int $projectId, ?string $entityType = null, ?int $entityId = null): array
    {
        $q = $this->select('project_files.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS uploader_name')
            ->join('fs_users', 'fs_users.id = project_files.uploaded_by', 'left')
            ->where('project_files.project_id', $projectId)
            ->where('project_files.deleted_at IS NULL', null, false)
            ->orderBy('project_files.created_at', 'DESC');

        if ($entityType) $q->where('project_files.entity_type', $entityType);
        if ($entityId)   $q->where('project_files.entity_id', $entityId);

        return $q->findAll();
    }

    /** Human-readable file size */
    public static function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    /** Icon class from mime type */
    public static function mimeIcon(string $mime): string
    {
        if (str_starts_with($mime, 'image/'))         return 'fa-file-image text-info';
        if ($mime === 'application/pdf')               return 'fa-file-pdf text-danger';
        if (str_contains($mime, 'word'))               return 'fa-file-word text-primary';
        if (str_contains($mime, 'excel') || str_contains($mime, 'sheet')) return 'fa-file-excel text-success';
        if (str_contains($mime, 'zip') || str_contains($mime, 'rar'))     return 'fa-file-zipper text-warning';
        if (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation')) return 'fa-file-powerpoint text-orange';
        return 'fa-file text-secondary';
    }
}
