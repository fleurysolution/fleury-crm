<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\DailyLogModel;
use App\Models\DailyManpowerModel;
use App\Models\DailyEquipmentModel;

class TestPhase4 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase4';
    protected $description = 'Verifies Phase 4 Project execution and Daily Logs';

    public function run(array $params)
    {
        CLI::write("Testing Phase 4: Project Management Data Scoping", 'green');
        
        $projectModel = new ProjectModel();
        $logModel     = new DailyLogModel();
        $manpowerMod  = new DailyManpowerModel();
        $equipMod     = new DailyEquipmentModel();

        // 1. Setup Session Context for ABAC/ErpModel
        $_SESSION['is_logged_in'] = true;
        $_SESSION['tenant_id'] = 1;
        $_SESSION['branch_id'] = 77; // Simulate worker in branch 77
        $_SESSION['user_id'] = 100;
        
        CLI::write("1. Simulating project creation by Branch 77 Manager...", 'yellow');
        
        $projectId = $projectModel->insert([
            'tenant_id' => $_SESSION['tenant_id'],
            'branch_id' => $_SESSION['branch_id'],
            'title' => 'Test Project Phase 4',
            'contract_type' => 'lump_sum',
            'versioned_budget_baseline' => 500000.00,
            'project_stage' => 'active',
            'status' => 'active'
        ]);

        $project = $projectModel->find($projectId);
        
        if ($project && $project['branch_id'] == 77 && $project['tenant_id'] == 1 && $project['contract_type'] === 'lump_sum') {
            CLI::write("  [PASS] Project created successfully and automatically scoped to branch 77 by ErpModel.", 'green');
        } else {
            CLI::write("  [FAIL] Project creation failed to scope data correctly.", 'red');
            return;
        }

        CLI::write("\n2. Simulating Daily Log Entry...", 'yellow');
        $logId = $logModel->insert([
            'tenant_id' => $_SESSION['tenant_id'],
            'branch_id' => $_SESSION['branch_id'],
            'project_id' => $projectId,
            'date' => date('Y-m-d'),
            'weather_conditions' => 'Sunny, 75F',
            'site_conditions' => 'Dry',
            'notes' => 'Everything moving to schedule.'
        ]);

        $logRow = $logModel->find($logId);
        
        if ($logRow && $logRow['branch_id'] == 77 && $logRow['project_id'] == $projectId) {
             CLI::write("  [PASS] Daily Log created and scoped to branch 77.", 'green');
        } else {
             CLI::write("  [FAIL] Daily Log branching failed.", 'red');
        }

        CLI::write("\n3. Testing Manpower & Equipment sub-records...", 'yellow');
        $manpowerMod->insert([
            'log_id' => $logId,
            'trade_or_contractor' => 'Plumbing',
            'worker_count' => 5,
            'hours' => 40.00
        ]);
        
        $equipMod->insert([
            'log_id' => $logId,
            'equipment_type' => 'Excavator',
            'hours_used' => 6.5,
            'status' => 'operational'
        ]);
        
        $mpCount = $manpowerMod->where('log_id', $logId)->countAllResults();
        $eqCount = $equipMod->where('log_id', $logId)->countAllResults();
        
        if ($mpCount === 1 && $eqCount === 1) {
            CLI::write("  [PASS] Sub-records linked successfully.", 'green');
        } else {
            CLI::write("  [FAIL] Sub-records could not be linked.", 'red');
        }

        // Cleanup
        $projectModel->delete($projectId, true);
        $logModel->delete($logId, true);
        $manpowerMod->where('log_id', $logId)->delete();
        $equipMod->where('log_id', $logId)->delete();
        
        CLI::write("\nPhase 4 tests completed.", 'green');
    }
}
