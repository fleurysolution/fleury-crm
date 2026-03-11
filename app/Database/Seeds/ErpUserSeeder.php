<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ErpUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $userModel = model('FsUserModel');
        
        $branches = $db->table('branches')->get()->getResultArray();
        if (empty($branches)) {
            echo "No branches found. Please run ErpTestDataSeeder first.\n";
            return;
        }

        $roles = [
            'pm' => $db->table('roles')->where('slug', 'pm')->get()->getRow('id'),
            'superintendent' => $db->table('roles')->where('slug', 'superintendent')->get()->getRow('id'),
            'engineer' => $db->table('roles')->where('slug', 'engineer')->get()->getRow('id'),
            'foreman' => $db->table('roles')->where('slug', 'foreman')->get()->getRow('id'),
        ];

        $generatedUsers = [];
        $defaultPassword = password_hash('Pass1234!', PASSWORD_DEFAULT);

        foreach ($branches as $branch) {
            $branchId = $branch['id'];
            $tenantId = $branch['tenant_id'];
            $branchCode = strtolower(preg_replace('/[^a-z0-9]/i', '', explode(' ', $branch['name'])[0]));

            // Create a Project Manager for this branch
            $pmEmail = "pm.{$branchCode}@bpms247.com";
            if ($userModel->where('email', $pmEmail)->countAllResults() == 0) {
                $userId = $userModel->insert([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'first_name' => 'PM',
                    'last_name' => $branch['name'],
                    'email' => $pmEmail,
                    'password_hash' => $defaultPassword,
                    'status' => 'active',
                    'geo_access_permission' => 'branch',
                    'approval_authority_level' => 3
                ]);
                $db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => $roles['pm']]);
                $generatedUsers[] = "Branch: {$branch['name']} | Role: PM | Email: {$pmEmail} | Pass: Pass1234!";
            }

            // Create a Superintendent
            $supEmail = "super.{$branchCode}@bpms247.com";
            if ($userModel->where('email', $supEmail)->countAllResults() == 0) {
                $userId = $userModel->insert([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'first_name' => 'Super',
                    'last_name' => $branch['name'],
                    'email' => $supEmail,
                    'password_hash' => $defaultPassword,
                    'status' => 'active',
                    'geo_access_permission' => 'branch',
                    'approval_authority_level' => 2
                ]);
                $db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => $roles['superintendent']]);
                $generatedUsers[] = "Branch: {$branch['name']} | Role: Superintendent | Email: {$supEmail} | Pass: Pass1234!";
            }
        }

        // Output to a file for the user to copy/paste easily
        $reportPath = FCPATH . 'erp_test_users.txt';
        file_put_contents($reportPath, "--- ERP Test User Accounts ---\n\n" . implode("\n", $generatedUsers));
        
        echo "Successfully seeded Erp Users.\n";
        echo "A detailed list has been generated at: " . $reportPath . "\n";
    }
}
