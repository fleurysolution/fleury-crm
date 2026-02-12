<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsUserRoleModel extends Model
{
    protected $table         = 'fs_as_user_roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'role_id',
        'assigned_by',
        'assigned_at',
    ];

    public $useTimestamps = false;
}
