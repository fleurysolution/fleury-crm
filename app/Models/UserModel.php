<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'fs_users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'email','password','user_type','client_id','is_admin','role',
        'first_name','last_name','locale','status','deleted','disable_login'
    ];

    public function findActiveByEmail(string $email): ?array
    {
        return $this->where([
                'email'         => $email,
                'status'        => 'active',
                'deleted'       => 0,
                'disable_login' => 0,
            ])->first();
    }
}