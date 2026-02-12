<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsPermissionModel extends Model
{
    protected $table          = 'fs_as_permissions';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'permission_key',
        'module_key',
        'display_name',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
}
