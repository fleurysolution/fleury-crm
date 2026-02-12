<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsRolePermissionModel extends Model
{
    protected $table         = 'fs_as_role_permissions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'role_id',
        'permission_id',
        'created_at',
    ];

    public $useTimestamps = false;
}
