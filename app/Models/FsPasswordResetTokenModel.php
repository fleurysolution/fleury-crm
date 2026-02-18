<?php

namespace App\Models;

use CodeIgniter\Model;

class FsPasswordResetTokenModel extends Model
{
    protected $table         = 'fs_password_reset_tokens';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';

    protected $allowedFields = [
        'user_id',
        'token_hash',
        'issued_ip',
        'user_agent',
        'expires_at',
        'used_at',
        'created_at',
    ];

    public $useTimestamps = false;
}
