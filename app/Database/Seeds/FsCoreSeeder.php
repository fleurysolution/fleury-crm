<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FsCoreSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Roles
        $roles = [
            ['role_key' => 'super_admin', 'display_name' => 'Super Admin', 'description' => 'Full access', 'is_system' => 1],
            ['role_key' => 'admin',       'display_name' => 'Admin',       'description' => 'Admin access', 'is_system' => 1],
            ['role_key' => 'manager',     'display_name' => 'Manager',     'description' => 'Manager access', 'is_system' => 1],
            ['role_key' => 'employee',    'display_name' => 'Employee',    'description' => 'Employee access', 'is_system' => 1],
        ];

        foreach ($roles as $role) {
            $exists = $this->db->table('fs_roles')->where('role_key', $role['role_key'])->get()->getRow();
            if (!$exists) {
                $this->db->table('fs_roles')->insert(array_merge($role, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        // Permissions
        $permissions = [
            ['permission_key' => 'iam.users.view', 'module_key' => 'iam', 'display_name' => 'View users'],
            ['permission_key' => 'iam.users.manage', 'module_key' => 'iam', 'display_name' => 'Manage users'],
            ['permission_key' => 'iam.roles.manage', 'module_key' => 'iam', 'display_name' => 'Manage roles'],
            ['permission_key' => 'approval.workflow.manage', 'module_key' => 'approval', 'display_name' => 'Manage workflows'],
            ['permission_key' => 'approval.request.view', 'module_key' => 'approval', 'display_name' => 'View requests'],
            ['permission_key' => 'approval.request.action', 'module_key' => 'approval', 'display_name' => 'Approve/reject requests'],
            ['permission_key' => 'audit.events.view', 'module_key' => 'audit', 'display_name' => 'View audit events'],
        ];

        foreach ($permissions as $perm) {
            $exists = $this->db->table('fs_permissions')->where('permission_key', $perm['permission_key'])->get()->getRow();
            if (!$exists) {
                $this->db->table('fs_permissions')->insert(array_merge($perm, [
                    'description' => null,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]));
            }
        }

        // Super Admin user
        $email = 'admin@fs.local';
        $user = $this->db->table('fs_users')->where('email', $email)->get()->getRow();

        if (!$user) {
            $this->db->table('fs_users')->insert([
                'employee_code'     => 'FS-0001',
                'email'             => $email,
                'password_hash'     => password_hash('ChangeMe@123', PASSWORD_DEFAULT),
                'first_name'        => 'System',
                'last_name'         => 'Admin',
                'phone'             => null,
                'avatar_url'        => null,
                'status'            => 'active',
                'email_verified_at' => $now,
                'last_login_at'     => null,
                'created_at'        => $now,
                'updated_at'        => $now,
                'deleted_at'        => null,
            ]);
            $user = $this->db->table('fs_users')->where('email', $email)->get()->getRow();
        }

        // Assign super_admin role
        $role = $this->db->table('fs_roles')->where('role_key', 'super_admin')->get()->getRow();
        if ($user && $role) {
            $exists = $this->db->table('fs_user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', $role->id)
                ->get()->getRow();

            if (!$exists) {
                $this->db->table('fs_user_roles')->insert([
                    'user_id'     => $user->id,
                    'role_id'     => $role->id,
                    'assigned_by' => $user->id,
                    'assigned_at' => $now,
                ]);
            }

            // Grant all permissions to super_admin role
            $perms = $this->db->table('fs_permissions')->get()->getResult();
            foreach ($perms as $p) {
                $existsRP = $this->db->table('fs_role_permissions')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $p->id)
                    ->get()->getRow();

                if (!$existsRP) {
                    $this->db->table('fs_role_permissions')->insert([
                        'role_id'       => $role->id,
                        'permission_id' => $p->id,
                        'created_at'    => $now,
                    ]);
                }
            }
        }
    }
}
