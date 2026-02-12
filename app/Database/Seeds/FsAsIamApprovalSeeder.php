<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FsAsIamApprovalSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1) Roles
        $roles = [
            [
                'role_key'     => 'super_admin',
                'display_name' => 'Super Admin',
                'description'  => 'Full system access',
                'is_system'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'role_key'     => 'admin',
                'display_name' => 'Admin',
                'description'  => 'Administrative user',
                'is_system'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'role_key'     => 'manager',
                'display_name' => 'Manager',
                'description'  => 'Team and approval manager',
                'is_system'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'role_key'     => 'employee',
                'display_name' => 'Employee',
                'description'  => 'Standard business user',
                'is_system'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        foreach ($roles as $role) {
            $exists = $this->db->table('fs_as_roles')
                ->where('role_key', $role['role_key'])
                ->get()->getRow();

            if (!$exists) {
                $this->db->table('fs_as_roles')->insert($role);
            }
        }

        // 2) Permissions
        $permissions = [
            // IAM
            ['permission_key' => 'iam.users.view',           'module_key' => 'iam',      'display_name' => 'View Users'],
            ['permission_key' => 'iam.users.create',         'module_key' => 'iam',      'display_name' => 'Create Users'],
            ['permission_key' => 'iam.users.update',         'module_key' => 'iam',      'display_name' => 'Update Users'],
            ['permission_key' => 'iam.users.delete',         'module_key' => 'iam',      'display_name' => 'Delete Users'],
            ['permission_key' => 'iam.roles.manage',         'module_key' => 'iam',      'display_name' => 'Manage Roles'],
            ['permission_key' => 'iam.permissions.manage',   'module_key' => 'iam',      'display_name' => 'Manage Permissions'],

            // Approvals
            ['permission_key' => 'approval.workflow.view',   'module_key' => 'approval', 'display_name' => 'View Approval Workflows'],
            ['permission_key' => 'approval.workflow.manage', 'module_key' => 'approval', 'display_name' => 'Manage Approval Workflows'],
            ['permission_key' => 'approval.request.view',    'module_key' => 'approval', 'display_name' => 'View Approval Requests'],
            ['permission_key' => 'approval.request.submit',  'module_key' => 'approval', 'display_name' => 'Submit Approval Requests'],
            ['permission_key' => 'approval.request.approve', 'module_key' => 'approval', 'display_name' => 'Approve/Reject Requests'],

            // Audit
            ['permission_key' => 'audit.events.view',        'module_key' => 'audit',    'display_name' => 'View Audit Events'],
        ];

        foreach ($permissions as $perm) {
            $payload = [
                'permission_key' => $perm['permission_key'],
                'module_key'     => $perm['module_key'],
                'display_name'   => $perm['display_name'],
                'description'    => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];

            $exists = $this->db->table('fs_as_permissions')
                ->where('permission_key', $perm['permission_key'])
                ->get()->getRow();

            if (!$exists) {
                $this->db->table('fs_as_permissions')->insert($payload);
            }
        }

        // 3) Super Admin user
        $superEmail = 'admin@fsas.local';
        $superUser = $this->db->table('fs_as_users')->where('email', $superEmail)->get()->getRow();

        if (!$superUser) {
            $this->db->table('fs_as_users')->insert([
                'employee_code'      => 'FSAS-0001',
                'email'              => $superEmail,
                'password_hash'      => password_hash('ChangeMe@123', PASSWORD_DEFAULT),
                'first_name'         => 'System',
                'last_name'          => 'Administrator',
                'phone'              => null,
                'avatar_url'         => null,
                'status'             => 'active',
                'email_verified_at'  => $now,
                'last_login_at'      => null,
                'created_at'         => $now,
                'updated_at'         => $now,
                'deleted_at'         => null,
            ]);

            $superUser = $this->db->table('fs_as_users')->where('email', $superEmail)->get()->getRow();
        }

        // 4) Attach super_admin role to super user
        $superAdminRole = $this->db->table('fs_as_roles')->where('role_key', 'super_admin')->get()->getRow();

        if ($superUser && $superAdminRole) {
            $exists = $this->db->table('fs_as_user_roles')
                ->where('user_id', $superUser->id)
                ->where('role_id', $superAdminRole->id)
                ->get()->getRow();

            if (!$exists) {
                $this->db->table('fs_as_user_roles')->insert([
                    'user_id'      => $superUser->id,
                    'role_id'      => $superAdminRole->id,
                    'assigned_by'  => $superUser->id,
                    'assigned_at'  => $now,
                ]);
            }
        }

        // 5) Give all permissions to super_admin role
        if ($superAdminRole) {
            $allPerms = $this->db->table('fs_as_permissions')->get()->getResult();

            foreach ($allPerms as $perm) {
                $exists = $this->db->table('fs_as_role_permissions')
                    ->where('role_id', $superAdminRole->id)
                    ->where('permission_id', $perm->id)
                    ->get()->getRow();

                if (!$exists) {
                    $this->db->table('fs_as_role_permissions')->insert([
                        'role_id'       => $superAdminRole->id,
                        'permission_id' => $perm->id,
                        'created_at'    => $now,
                    ]);
                }
            }
        }
    }
}
