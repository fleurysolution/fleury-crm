<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\RfiModel;
use App\Models\SubmittalModel;
use App\Models\DrawingModel;

class TestPhase5 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase5';
    protected $description = 'Verifies Phase 5 Field Collaboration data linkage';

    public function run(array $params)
    {
        CLI::write("Testing Phase 5: Field Collaboration ABAC Scope", 'green');
        
        $projectModel   = new ProjectModel();
        $rfiModel       = new RfiModel();
        $submittalModel = new SubmittalModel();
        $drawingModel   = new DrawingModel();

        // 1. Setup Session Context for ABAC/ErpModel
        $_SESSION['is_logged_in'] = true;
        $_SESSION['tenant_id'] = 2; // Separate Tenant mapping
        $_SESSION['branch_id'] = 101; 
        $_SESSION['user_id'] = 505;
        $_SESSION['geo_access_permission'] = 'branch';
        $_SESSION['user_roles'] = ['project_manager'];
        
        CLI::write("1. Simulating project creation by Branch 101 PM...", 'yellow');
        
        $projectId = $projectModel->insert([
            'tenant_id' => 2,
            'branch_id' => 101,
            'title' => 'Test Project Phase 5',
            'contract_type' => 'lump_sum',
            'status' => 'active'
        ]);

        CLI::write("\n2. Creating Sub-Documents (RFI, Submittal, Drawing)...", 'yellow');
        
        $rfiId = $rfiModel->insert([
            'tenant_id'  => 2,
            'branch_id'  => 101,
            'project_id' => $projectId,
            'rfi_number' => 'RFI-001',
            'title'      => 'Missing Rebar Spec',
            'question'   => 'What size rebar is required for the foundation footing?'
        ]);

        $subId = $submittalModel->insert([
            'tenant_id'        => 2,
            'branch_id'        => 101,
            'project_id'       => $projectId,
            'submittal_number' => 'SUB-001',
            'title'            => 'Concrete Mix Design',
        ]);

        $drawId = $drawingModel->insert([
            'tenant_id'      => 2,
            'branch_id'      => 101,
            'project_id'     => $projectId,
            'discipline'     => 'Structural',
            'drawing_number' => 'S-100',
            'title'          => 'Foundation Plan',
            'revision'       => '0'
        ]);
        
        // Assertions
        $rfiCount = $rfiModel->where('project_id', $projectId)->countAllResults();
        $subCount = $submittalModel->where('project_id', $projectId)->countAllResults();
        $drwCount = $drawingModel->where('project_id', $projectId)->countAllResults();
        
        if ($rfiCount === 1 && $subCount === 1 && $drwCount === 1) {
            CLI::write("  [PASS] Field Collaboration documents inserted and retrieved under current branch scope successfully.", 'green');
        } else {
            CLI::write("  [FAIL] Document retrieval failed. Possible ABAC scope mismatch.", 'red');
            return;
        }

        // Test Cross-Branch Access Denied
        CLI::write("\n3. Testing Cross-Branch Scope Block...", 'yellow');
        $_SESSION['branch_id'] = 202; // Change session branch to assert block
        
        // The models have `beforeFind` hooks via ErpModel that inject `WHERE branch_id = 202`
        $rfiCrossCount = $rfiModel->where('project_id', $projectId)->countAllResults();
        
        if ($rfiCrossCount === 0) {
            CLI::write("  [PASS] Branch 202 User was strictly blocked from seeing Branch 101 RFI data.", 'green');
        } else {
            CLI::write("  [FAIL] ABAC Leak: Branch 202 user fetched Branch 101 data.", 'red');
        }

        // Cleanup bypassing ABAC
        $_SESSION['user_roles'] = ['admin']; // Admin clears scope
        $projectModel->delete($projectId, true);
        $rfiModel->delete($rfiId, true);
        $submittalModel->delete($subId, true);
        $drawingModel->delete($drawId, true);
        
        CLI::write("\nPhase 5 tests completed.", 'green');
    }
}
