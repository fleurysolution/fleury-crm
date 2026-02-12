<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsUserPermissionModel extends Model
{
    protected $table         = 'fs_as_user_permissions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'permission_id',
        'effect',
        'assigned_by',
        'assigned_at',
    ];

    public $useTimestamps = false;
}
