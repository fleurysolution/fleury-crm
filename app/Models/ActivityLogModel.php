<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table         = 'activity_log';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'project_id','entity_type','entity_id','action',
        'description','old_values','new_values','user_id','ip_address','created_at',
    ];

    // ── Query helpers ─────────────────────────────────────────────────────────

    /**
     * Activity feed for a project, newest first, with actor name joined.
     */
    public function forProject(int $projectId, int $limit = 100): array
    {
        return $this->select('activity_log.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS actor_name, fs_users.avatar_url AS actor_avatar')
            ->join('fs_users', 'fs_users.id = activity_log.user_id', 'left')
            ->where('activity_log.project_id', $projectId)
            ->orderBy('activity_log.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Global activity feed (admin), with project title joined.
     */
    public function globalFeed(int $limit = 100): array
    {
        $db = \Config\Database::connect();
        return $db->query(
            'SELECT al.*, CONCAT(u.first_name, " ", u.last_name) AS actor_name, u.avatar_url AS actor_avatar,
                    p.title AS project_title
             FROM activity_log al
             LEFT JOIN fs_users u  ON u.id  = al.user_id
             LEFT JOIN projects  p ON p.id  = al.project_id
             ORDER BY al.created_at DESC
             LIMIT ?',
            [$limit]
        )->getResultArray();
    }

    /**
     * Log a single action. Call this from controllers after mutations.
     */
    public static function log(
        string $entityType,
        int    $entityId,
        string $action,
        string $description = '',
        array  $extra       = []
    ): void {
        $m          = new self();
        $request    = \Config\Services::request();
        $session    = \Config\Services::session();
        $user       = $session->get('user');

        $m->insert([
            'project_id'  => $extra['project_id']  ?? null,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'action'      => $action,
            'description' => $description ?: null,
            'old_values'  => isset($extra['old']) ? json_encode($extra['old']) : null,
            'new_values'  => isset($extra['new']) ? json_encode($extra['new']) : null,
            'user_id'     => $user['id'] ?? null,
            'ip_address'  => $request->getIPAddress(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
