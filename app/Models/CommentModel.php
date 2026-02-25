<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table         = 'comments';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'project_id','entity_type','entity_id','parent_id',
        'body','user_id','created_at','updated_at','deleted_at',
    ];

    // ── Query helpers ───────────────────────────────────────────────────────

    /**
     * Get all (non-deleted) comments for a given entity, with author info.
     * Returns flat list; client-side JS threads by parent_id.
     */
    public function forEntity(string $entityType, int $entityId): array
    {
        return $this->select('comments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name, fs_users.avatar AS author_avatar')
            ->join('fs_users', 'fs_users.id = comments.user_id', 'left')
            ->where('comments.entity_type', $entityType)
            ->where('comments.entity_id', $entityId)
            ->where('comments.deleted_at IS NULL', null, false)
            ->orderBy('comments.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Soft delete a comment (only by author or admin).
     */
    public function softDelete(int $id): void
    {
        $this->where('id', $id)->set('deleted_at', date('Y-m-d H:i:s'))->update();
    }

    /**
     * Post a comment.
     */
    public static function post(
        string $entityType,
        int    $entityId,
        string $body,
        int    $userId,
        ?int   $projectId = null,
        ?int   $parentId  = null
    ): int {
        $m = new self();
        $m->insert([
            'project_id'  => $projectId,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'parent_id'   => $parentId,
            'body'        => $body,
            'user_id'     => $userId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        return $m->db->insertID();
    }
}
