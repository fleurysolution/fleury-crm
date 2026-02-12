<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsRoleModel extends Model
{
    protected $table          = 'fs_as_roles';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'role_key',
        'display_name',
        'description',
        'is_system',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
}
