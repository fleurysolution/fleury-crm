<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsUserModel extends Model
{
    protected $table            = 'fs_as_users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
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

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'email'         => 'required|valid_email|max_length[190]',
        'password_hash' => 'required|max_length[255]',
        'first_name'    => 'required|max_length[100]',
        'status'        => 'required|in_list[active,inactive,suspended]',
    ];
}
