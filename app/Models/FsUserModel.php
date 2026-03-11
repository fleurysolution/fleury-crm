<?php

namespace App\Models;

use CodeIgniter\Model;

class FsUserModel extends ErpModel
{
    protected $table          = 'fs_users';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    // Users are tenant-level, they don't necessarily belong to a single branch for access purposes,
    // though they have a primary branch_id. We handle their access via geo_access_permission.
    protected $enforceBranchLinkage = false;

    protected $allowedFields  = [
        'tenant_id',
        'branch_id',
        'department_id',
        'geo_access_permission',
        'employee_code',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'avatar_url',
        'status',
        'email_verified_at',
        'last_login_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    /**
     * Get roles associated with the user.
     */
    public function getRoles(int $userId): array
    {
        $db = \Config\Database::connect();
        return $db->table('user_roles')
            ->select('roles.*')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all permissions associated with the user (via roles).
     */
    public function getPermissions(int $userId): array
    {
        $db = \Config\Database::connect();
        return $db->table('user_roles')
            ->select('permissions.slug')
            ->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('user_roles.user_id', $userId)
            ->distinct()
            ->get()
            ->getResultArray();
    }

    /**
     * Compatibility: Get the currently logged in user ID from session.
     */
    public function login_user_id(): ?int
    {
        return session()->get('user_id');
    }

    /**
     * Compatibility: Get one user by ID.
     */
    public function get_one(int $id): ?object
    {
        // Return as object to match BaseAppController expectations
        $row = $this->find($id);
        return $row ? (object)$row : null;
    }
}
