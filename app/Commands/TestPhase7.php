<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\PurchaseOrderModel;
use App\Models\BidModel;

class TestPhase7 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase7';
    protected $description = 'Verifies Phase 7 Procurement data linkage';

    public function run(array $params)
    {
        CLI::write("Testing Phase 7: Procurement ABAC Scope (Purchase Orders, Bids)", 'green');
        
        $projectModel = new ProjectModel();
        $poModel      = new PurchaseOrderModel();
        $bidModel     = new BidModel();

        // 1. Setup Session Context for ABAC/ErpModel
        $_SESSION['is_logged_in'] = true;
        $_SESSION['tenant_id'] = 1; 
        $_SESSION['branch_id'] = 12; // Target branch
        $_SESSION['user_id'] = 333;
        $_SESSION['geo_access_permission'] = 'branch';
        $_SESSION['user_roles'] = ['procurement_manager'];
        
        CLI::write("1. Simulating project creation by Branch 12 Procurement Manager...", 'yellow');
        
        $projectId = $projectModel->insert([
            'tenant_id' => 1,
            'branch_id' => 12,
            'title' => 'Test Project Phase 7',
            'contract_type' => 'lump_sum',
            'status' => 'active'
        ]);

        CLI::write("\n2. Creating Procurement Records (PO, Bid)...", 'yellow');
        
        $poId = $poModel->insert([
            'tenant_id'    => 1,
            'branch_id'    => 12,
            'project_id'   => $projectId,
            'po_number'    => 'PO-TEST',
            'title'        => 'Lumber Order',
            'status'       => 'Draft',
            'total_amount' => 5000.00,
            'created_by'   => 333
        ]);

        $bidId = $bidModel->insert([
            'tenant_id'     => 1,
            'branch_id'     => 12,
            'project_id'    => $projectId,
            'trade_package' => 'Electrical',
            'vendor_name'   => 'Sparky Electrics',
            'bid_amount'    => 15000.00,
            'status'        => 'Pending',
            'created_by'    => 333
        ]);
        
        // Assertions
        $poCount = $poModel->where('project_id', $projectId)->countAllResults();
        $bidCount= $bidModel->where('project_id', $projectId)->countAllResults();
        
        if ($poCount === 1 && $bidCount === 1) {
            CLI::write("  [PASS] Procurement documents inserted and retrieved under current branch scope successfully.", 'green');
        } else {
            CLI::write("  [FAIL] Document retrieval failed. Possible ABAC scope mismatch.", 'red');
            return;
        }

        // Test Cross-Branch Access Denied
        CLI::write("\n3. Testing Cross-Branch Scope Block...", 'yellow');
        $_SESSION['branch_id'] = 88; // Change session branch to assert block
        
        $poCrossCount = $poModel->where('project_id', $projectId)->countAllResults();
        $bidCrossCount= $bidModel->where('project_id', $projectId)->countAllResults();
        
        if ($poCrossCount === 0 && $bidCrossCount === 0) {
            CLI::write("  [PASS] Branch 88 User was strictly blocked from seeing Branch 12 Procurement data.", 'green');
        } else {
            CLI::write("  [FAIL] ABAC Leak: Branch 88 user fetched Branch 12 data.", 'red');
        }

        // Cleanup bypassing ABAC
        $_SESSION['user_roles'] = ['admin']; // Admin clears scope
        $projectModel->delete($projectId, true);
        $poModel->delete($poId, true);
        $bidModel->delete($bidId, true);
        
        CLI::write("\nPhase 7 tests completed.", 'green');
    }
}
