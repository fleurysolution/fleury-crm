<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\InspectionModel;
use App\Models\SafetyIncidentModel;
use App\Models\PunchListItemModel;

class TestPhase6 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase6';
    protected $description = 'Verifies Phase 6 QHSE data linkage';

    public function run(array $params)
    {
        CLI::write("Testing Phase 6: QHSE ABAC Scope (Inspections, Safety, Punch Lists)", 'green');
        
        $projectModel   = new ProjectModel();
        $inspModel      = new InspectionModel();
        $safetyModel    = new SafetyIncidentModel();
        $punchModel     = new PunchListItemModel();

        // 1. Setup Session Context for ABAC/ErpModel
        $_SESSION['is_logged_in'] = true;
        $_SESSION['tenant_id'] = 1; 
        $_SESSION['branch_id'] = 58; // Target branch
        $_SESSION['user_id'] = 712;
        $_SESSION['geo_access_permission'] = 'branch';
        $_SESSION['user_roles'] = ['safety_officer'];
        
        CLI::write("1. Simulating project creation by Branch 58 Safety Officer...", 'yellow');
        
        $projectId = $projectModel->insert([
            'tenant_id' => 1,
            'branch_id' => 58,
            'title' => 'Test Project Phase 6',
            'contract_type' => 'lump_sum',
            'status' => 'active'
        ]);

        CLI::write("\n2. Creating QHSE Records (Inspection, Incident, Punch List)...", 'yellow');
        
        $inspId = $inspModel->insert([
            'tenant_id'  => 1,
            'branch_id'  => 58,
            'project_id' => $projectId,
            'type'       => 'Scaffolding Safety',
            'status'     => 'completed'
        ]);

        $safeId = $safetyModel->insert([
            'tenant_id'     => 1,
            'branch_id'     => 58,
            'project_id'    => $projectId,
            'incident_date' => date('Y-m-d H:i:s'),
            'type'          => 'near_miss',
            'severity'      => 'medium',
            'description'   => 'Dropped hammer almost hit worker.'
        ]);

        $punchId = $punchModel->insert([
            'tenant_id'   => 1,
            'branch_id'   => 58,
            'project_id'  => $projectId,
            'item_number' => 'PL-0001',
            'title'       => 'Paint peeling in hallway',
            'status'      => 'open'
        ]);
        
        // Assertions
        $inspCount = $inspModel->where('project_id', $projectId)->countAllResults();
        $safeCount = $safetyModel->where('project_id', $projectId)->countAllResults();
        $punchCount= $punchModel->where('project_id', $projectId)->countAllResults();
        
        if ($inspCount === 1 && $safeCount === 1 && $punchCount === 1) {
            CLI::write("  [PASS] QHSE documents inserted and retrieved under current branch scope successfully.", 'green');
        } else {
            CLI::write("  [FAIL] Document retrieval failed. Possible ABAC scope mismatch.", 'red');
            return;
        }

        // Test Cross-Branch Access Denied
        CLI::write("\n3. Testing Cross-Branch Scope Block...", 'yellow');
        $_SESSION['branch_id'] = 99; // Change session branch to assert block
        
        $inspCrossCount = $inspModel->where('project_id', $projectId)->countAllResults();
        $safeCrossCount = $safetyModel->where('project_id', $projectId)->countAllResults();
        $punchCrossCount= $punchModel->where('project_id', $projectId)->countAllResults();
        
        if ($inspCrossCount === 0 && $safeCrossCount === 0 && $punchCrossCount === 0) {
            CLI::write("  [PASS] Branch 99 User was strictly blocked from seeing Branch 58 QHSE data.", 'green');
        } else {
            CLI::write("  [FAIL] ABAC Leak: Branch 99 user fetched Branch 58 data.", 'red');
        }

        // Cleanup bypassing ABAC
        $_SESSION['user_roles'] = ['admin']; // Admin clears scope
        $projectModel->delete($projectId, true);
        $inspModel->delete($inspId, true);
        $safetyModel->delete($safeId, true);
        $punchModel->delete($punchId, true);
        
        CLI::write("\nPhase 6 tests completed.", 'green');
    }
}
