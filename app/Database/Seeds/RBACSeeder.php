<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RBACSeeder extends Seeder
{
    public function run()
    {
        $roleModel = model('RoleModel');
        $permissionModel = model('PermissionModel');
        $db = \Config\Database::connect();

        // 1. Create Roles
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full access to all system features.',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Can manage teams and view reports.',
            ],
            [
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Standard access to assigned tasks and projects.',
            ],
        ];

        foreach ($roles as $role) {
            if (!$roleModel->where('slug', $role['slug'])->first()) {
                $roleModel->insert($role);
            }
        }

        // 2. Create Permissions
        $permissions = [
            ['name' => 'Manage Team', 'slug' => 'manage_team', 'description' => 'Create and edit team members'],
            ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'description' => 'Create and edit roles'],
            ['name' => 'View Reports', 'slug' => 'view_reports', 'description' => 'View system reports'],
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'description' => 'Edit system settings'],
        ];

        foreach ($permissions as $permission) {
            if (!$permissionModel->where('slug', $permission['slug'])->first()) {
                $permissionModel->insert($permission);
            }
        }

        // 3. Assign Permissions to Roles
        // Admin gets all permissions (handled via code check, but good to have in DB too)
        $adminRole = $roleModel->where('slug', 'admin')->first();
        $managerRole = $roleModel->where('slug', 'manager')->first();
        
        $allPermissions = $permissionModel->findAll();
        
        // Assign all to Admin
        foreach ($allPermissions as $perm) {
            // Check if exists
            $exists = $db->table('role_permissions')
                ->where('role_id', $adminRole['id'])
                ->where('permission_id', $perm['id'])
                ->countAllResults();
            
            if (!$exists) {
                $db->table('role_permissions')->insert([
                    'role_id' => $adminRole['id'],
                    'permission_id' => $perm['id']
                ]);
            }
        }

        // Assign some to Manager
        $managerPermissions = ['view_reports', 'manage_team'];
        foreach ($managerPermissions as $slug) {
             $perm = $permissionModel->where('slug', $slug)->first();
             if ($perm) {
                 $exists = $db->table('role_permissions')
                    ->where('role_id', $managerRole['id'])
                    ->where('permission_id', $perm['id'])
                    ->countAllResults();
                
                if (!$exists) {
                    $db->table('role_permissions')->insert([
                        'role_id' => $managerRole['id'],
                        'permission_id' => $perm['id']
                    ]);
                }
             }
        }
    }
}
