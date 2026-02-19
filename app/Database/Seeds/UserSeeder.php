<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = model('FsUserModel');
        $roleModel = model('RoleModel');
        $db = \Config\Database::connect();

        // 1. Create Admin User
        $adminUser = [
            'first_name'    => 'System',
            'last_name'     => 'Administrator',
            'email'         => 'admin@bpms.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Check if user exists
        $existingUser = $userModel->where('email', $adminUser['email'])->first();

        if (!$existingUser) {
            $userId = $userModel->insert($adminUser);
            echo "Admin user created with ID: $userId\n";
        } else {
            $userId = $existingUser['id'];
            echo "Admin user already exists with ID: $userId\n";
        }

        // 2. Assign Admin Role
        $adminRole = $roleModel->where('slug', 'admin')->first();

        if ($adminRole && $userId) {
            $exists = $db->table('user_roles')
                ->where('user_id', $userId)
                ->where('role_id', $adminRole['id'])
                ->countAllResults();

            if (!$exists) {
                $db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $adminRole['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Admin role assigned to user.\n";
            }
        }
    }
}
