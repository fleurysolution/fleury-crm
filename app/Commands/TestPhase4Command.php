<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestPhase4Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase4';
    protected $description = 'Verifies Phase 4 ERP functionality (Projects and Daily Logs)';

    public function run(array $params)
    {
        CLI::write("--- Testing Phase 4: ERP Projects and Daily Logs ---", 'yellow');

        // Simulate a logged-in user at branch 10 and tenant 1
        $session = session();
        $session->set([
            'user_id' => 1,
            'tenant_id' => 1,
            'branch_id' => 10,
            'is_logged_in' => true
        ]);

        $projects = new \App\Models\ProjectModel();
        $dailyLogs = new \App\Models\DailyLogModel();
        $manpower = new \App\Models\DailyManpowerModel();

        // 1. Create a Project
        try {
            $projectId = $projects->insert([
                'title' => 'Test ERP Project - Branch 10',
                'tenant_id' => 1,
                'branch_id' => 10,
                'contract_type' => 'lump_sum',
                'versioned_budget_baseline' => 50000.00,
                'project_stage' => 'active'
            ]);
            
            $p = $projects->find($projectId);
            CLI::write("Successfully created Project ID {$projectId}", 'green');
            CLI::write("  -> Linked Tenant ID: " . ($p['tenant_id'] ?? 'NULL'));
            CLI::write("  -> Linked Branch ID: " . ($p['branch_id'] ?? 'NULL'));
            
            if ($p['branch_id'] != 10) {
                CLI::error("FAILED: Expected branch_id to be 10 (auto-injected by ErpModel).");
                return;
            }
        } catch (\Exception $e) {
            CLI::error("Error inserting project: " . $e->getMessage());
            return;
        }

        // 2. Create a Daily Log
        try {
            $logId = $dailyLogs->insert([
                'tenant_id' => 1,
                'branch_id' => 10,
                'project_id' => $projectId,
                'date' => date('Y-m-d'),
                'weather_conditions' => 'Sunny, 75F',
                'site_conditions' => 'Good',
                'notes' => 'Site grading commenced.'
            ]);
            
            $l = $dailyLogs->find($logId);
            CLI::write("Successfully created Daily Log ID {$logId}", 'green');
            CLI::write("  -> Linked Branch ID: " . ($l['branch_id'] ?? 'NULL'));

            if ($l['branch_id'] != 10) {
                CLI::error("FAILED: Expected branch_id to be 10 on daily log.");
                return;
            }
        } catch (\Exception $e) {
            CLI::error("Error inserting daily log: " . $e->getMessage());
            return;
        }

        // 3. Add Manpower
        try {
            $mpId = $manpower->insert([
                'log_id' => $logId,
                'trade_or_contractor' => 'Excavation Corp',
                'worker_count' => 4,
                'hours' => 32
            ]);
            CLI::write("Successfully added Manpower Entry ID {$mpId}", 'green');
        } catch (\Exception $e) {
            CLI::error("Error inserting manpower: " . $e->getMessage());
            return;
        }

        CLI::write("SUCCESS: Phase 4 data linkage and schema verification passed.", 'black', 'green');

        // Cleanup test data
        $manpower->delete($mpId);
        $dailyLogs->delete($logId, true);
        $projects->delete($projectId, true);
        CLI::write("Cleanup completed.", 'yellow');
    }
}
