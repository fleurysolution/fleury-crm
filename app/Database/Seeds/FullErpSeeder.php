<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FullErpSeeder extends Seeder
{
    public function run()
    {
        // 1. Foundation: Tenants, Regions, Branches, Roles, Departments
        $this->call('ErpTestDataSeeder');
        echo "Foundation (Tenants, Branches, Roles) seeded.\n";

        // 2. Users: Project Managers, Superintendents, etc.
        $this->call('ErpUserSeeder');
        echo "Users (PMs, Supers) seeded.\n";

        // 3. Project & Field Data: Clients, Projects, RFIs, Submittals, Logs
        $this->seedProjectOperations();
        echo "Project Operations (RFIs, Submittals, Site Diaries) seeded.\n";

        // 4. Financials: Expenses, Invoices, Workflows
        $this->seedFinancialsAndWorkflows();
        echo "Financials (Expenses, Invoices, Workflows) seeded.\n";

        // 5. Assets & Inventory
        $this->seedAssetsAndInventory();
        echo "Assets & Inventory seeded.\n";

        echo "FULL ERP TEST DATA SEEDING COMPLETE!\n";
    }

    private function seedProjectOperations()
    {
        $db = \Config\Database::connect();
        $projects = $db->table('projects')->get()->getResultArray();
        $adminId = $db->table('fs_users')->where('email', 'admin@bpms.com')->get()->getRow('id') ?? 1;
        $now = date('Y-m-d H:i:s');

        // Clear existing to avoid dupes/conflicts
        $db->table('fs_rfis')->truncate();
        $db->table('fs_submittals')->truncate();
        $db->table('project_site_diaries')->truncate();
        $db->table('fs_daily_logs')->truncate();

        foreach ($projects as $project) {
            $projectId = $project['id'];
            $tenantId  = $project['tenant_id'] ?? 1;
            $branchId  = $project['branch_id'] ?? 8;

            // RFIs (fs_rfis)
            $db->table('fs_rfis')->insert([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'project_id' => $projectId,
                'rfi_number' => "RFI-{$projectId}-001",
                'title' => 'Structural Column Displacement',
                'question' => 'The column at Grid B4 seems displaced by 50mm. Please advise.',
                'status' => 'open',
                'created_by' => $adminId,
                'created_at' => $now
            ]);

            // Submittals (fs_submittals)
            $db->table('fs_submittals')->insert([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'project_id' => $projectId,
                'submittal_number' => "SUB-{$projectId}-001",
                'title' => 'Grade 60 Rebar Mill Certs',
                'spec_section' => '03 30 00',
                'status' => 'submitted',
                'created_by' => $adminId,
                'created_at' => $now
            ]);

            // Site Diaries (project_site_diaries - Legacy)
            $db->table('project_site_diaries')->insert([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'project_id' => $projectId,
                'report_date' => date('Y-m-d'),
                'weather_conditions' => 'Overcast',
                'temperature' => '18C',
                'status' => 'Draft',
                'created_at' => $now
            ]);

            // Daily Logs (fs_daily_logs - New Structure)
            $db->query("INSERT INTO fs_daily_logs (tenant_id, branch_id, project_id, date, weather_conditions, status, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
                $tenantId, $branchId, $projectId, date('Y-m-d'), 'Overcast', 'draft', $adminId, $now
            ]);
            $logId = $db->insertID();

            // Manpower (fs_daily_manpower)
            $db->table('fs_daily_manpower')->insert([
                'log_id' => $logId,
                'trade_or_contractor' => 'Concrete Crew',
                'worker_count' => 5,
                'hours' => 40,
                'notes' => 'Pouring foundation slabs'
            ]);

            // Equipment (fs_daily_equipment)
            $db->table('fs_daily_equipment')->insert([
                'log_id' => $logId,
                'equipment_type' => 'Excavator',
                'hours_used' => 8,
                'status' => 'Operational'
            ]);

            // Drawings (fs_drawings)
            $db->table('fs_drawings')->insert([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'project_id' => $projectId,
                'discipline' => 'Structural',
                'drawing_number' => "S-{$projectId}-01",
                'title' => 'Foundation Plan',
                'revision' => 'A',
                'status' => 'active',
                'created_by' => $adminId,
                'created_at' => $now
            ]);
        }
    }

    private function seedFinancialsAndWorkflows()
    {
        $db = \Config\Database::connect();
        $projects = $db->table('projects')->get()->getResultArray();
        $adminId = $db->table('fs_users')->where('email', 'admin@bpms.com')->get()->getRow('id') ?? 1;
        $now = date('Y-m-d H:i:s');

        $db->table('project_expenses')->truncate();
        $db->table('project_invoices')->truncate();
        $db->table('fs_as_approval_workflows')->truncate();

        foreach ($projects as $project) {
            $tenantId = $project['tenant_id'] ?? 1;
            $branchId = $project['branch_id'] ?? 8;

            // Expenses (project_expenses)
            $db->query("INSERT INTO project_expenses (tenant_id, branch_id, project_id, category, amount, currency, description, status, expense_date, submitted_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $tenantId, $branchId, $project['id'], 'Materials', 1250.50, 'USD', 'Emergency plumbing fixtures', 'pending', date('Y-m-d'), $adminId, $now
            ]);

            // Invoices (project_invoices)
            $db->query("INSERT INTO project_invoices (tenant_id, branch_id, project_id, invoice_number, party_name, total_amount, status, invoice_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $tenantId, $branchId, $project['id'], "INV-" . rand(1000, 9000), 'Test Client Corp', 50000, 'draft', date('Y-m-d'), $now
            ]);
        }

        // Approval Workflows
        $relevantBranches = [8, 10, 16, 25, 32];
        foreach ($relevantBranches as $bId) {
            $db->table('fs_as_approval_workflows')->insert([
                'branch_id' => $bId,
                'workflow_key' => "WF_EXPENSE_{$bId}",
                'module_key'=> 'expenses',
                'entity_key'=> 'project_expense',
                'name'      => "Expense Approval B{$bId}",
                'min_amount'=> 0,
                'max_amount'=> 100000,
                'created_at'=> $now
            ]);

            // Also Site Diary workflow
            $db->table('fs_as_approval_workflows')->insert([
                'branch_id' => $bId,
                'workflow_key' => "WF_DIARY_{$bId}",
                'module_key'=> 'site_diaries',
                'entity_key'=> 'site_diary',
                'name'      => "Site Diary Approval B{$bId}",
                'min_amount'=> 0,
                'max_amount'=> 0,
                'created_at'=> $now
            ]);
        }
    }

    private function seedAssetsAndInventory()
    {
        $db = \Config\Database::connect();
        $branches = $db->table('branches')->get()->getResultArray();
        $now = date('Y-m-d H:i:s');

        $db->table('fs_assets')->truncate();
        $db->table('fs_inventory_locations')->truncate();
        $db->table('fs_inventory_items')->truncate();
        $db->table('fs_inventory_stocks')->truncate();

        foreach ($branches as $branch) {
            if (in_array($branch['id'], [8, 10, 16, 25, 32])) {
                $tenantId = $branch['tenant_id'];
                $branchId = $branch['id'];

                // Assets (fs_assets)
                $db->table('fs_assets')->insert([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'name' => 'Excavator CAT-320',
                    'asset_tag' => 'EQ-' . $branchId . '-001',
                    'category' => 'Heavy Equipment',
                    'status' => 'Active',
                    'created_at' => $now
                ]);

                // Inventory Locations (fs_inventory_locations)
                $locId = $db->table('fs_inventory_locations')->insert([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'name' => "Yard B{$branchId}",
                    'created_at' => $now
                ]);

                // Inventory Items (fs_inventory_items)
                $itemId = $db->table('fs_inventory_items')->insert([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'name' => 'Grade 40 12mm Rebar',
                    'sku' => "RB-12-G40-B{$branchId}",
                    'category' => 'Steel',
                    'unit_of_measure' => 'ton',
                    'created_at' => $now
                ]);

                // Initial Stock
                $db->table('fs_inventory_stocks')->insert([
                    'item_id' => $itemId,
                    'location_id' => $locId,
                    'quantity' => 50,
                    'updated_at' => $now
                ]);
            }
        }
    }
}
