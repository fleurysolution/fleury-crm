<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false; // manual created_at only
    protected $allowedFields = [
        'user_id','type','title','body','url','icon','color',
        'related_type','related_id','is_read','read_at','created_at',
    ];

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Un-read count for a user (used by header bell badge).
     */
    public function unreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    /**
     * Paginated list for a user, newest first.
     */
    public function forUser(int $userId, int $limit = 30): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Mark a single notification read.
     */
    public function markRead(int $id, int $userId): void
    {
        $this->where('id', $id)->where('user_id', $userId)
            ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Mark all as read for a user.
     */
    public function markAllRead(int $userId): void
    {
        $this->where('user_id', $userId)->where('is_read', 0)
            ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Create a notification. Called from controllers or a helper.
     */
    public static function send(int $userId, string $type, string $title, array $opts = []): void
    {
        $m = new self();
        $m->insert([
            'user_id'      => $userId,
            'type'         => $type,
            'title'        => $title,
            'body'         => $opts['body']         ?? null,
            'url'          => $opts['url']          ?? null,
            'icon'         => $opts['icon']         ?? 'fa-bell',
            'color'        => $opts['color']        ?? 'primary',
            'related_type' => $opts['related_type'] ?? null,
            'related_id'   => $opts['related_id']   ?? null,
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
