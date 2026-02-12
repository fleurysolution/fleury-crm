<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsUserSessionModel extends Model
{
    protected $table         = 'fs_as_user_sessions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity_at',
        'expires_at',
        'revoked_at',
    ];

    public $useAutoIncrement = false;
    public $useTimestamps    = false;
}
