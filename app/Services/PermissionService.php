<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class PermissionService
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function hasPermission(int $userId, string $permissionKey): bool
    {
        if ($userId <= 0 || $permissionKey === '') {
            return false;
        }

        // 1) Direct user permission override
        $direct = $this->db->table('fs_user_permissions up')
            ->select('up.effect')
            ->join('fs_permissions p', 'p.id = up.permission_id', 'inner')
            ->where('up.user_id', $userId)
            ->where('p.permission_key', $permissionKey)
            ->get()->getRowArray();

        if ($direct) {
            return ($direct['effect'] ?? 'deny') === 'allow';
        }

        // 2) Role-based permission
        $row = $this->db->table('fs_user_roles ur')
            ->select('p.id')
            ->join('fs_role_permissions rp', 'rp.role_id = ur.role_id', 'inner')
            ->join('fs_permissions p', 'p.id = rp.permission_id', 'inner')
            ->where('ur.user_id', $userId)
            ->where('p.permission_key', $permissionKey)
            ->get()->getRowArray();

        return (bool) $row;
    }
}
