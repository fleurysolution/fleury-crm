<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FsApprovalWorkflowModel;
use App\Models\FsApprovalWorkflowStepModel;
use App\Models\FsApprovalRequestModel;
use App\Models\PurchaseOrderModel;
use App\Services\WorkflowEngine;

class TestPhase13 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase13';
    protected $description = 'Verifies Phase 13 Workflow Engine integration';

    public function run(array $params)
    {
        CLI::write("Testing Phase 13: Workflow Engine Integration", 'green');
        
        $wfModel = new FsApprovalWorkflowModel();
        $stepModel = new FsApprovalWorkflowStepModel();
        $poModel = new PurchaseOrderModel();
        $reqModel = new FsApprovalRequestModel();
        
        // 1. Setup Mock Workflow for Branch 99, POs above 10K
        $wfId = $wfModel->insert([
            'workflow_key' => 'PO_HIGH_VALUE_99',
            'module_key'   => 'purchase_orders',
            'branch_id'    => 99,
            'name'         => 'High Value POs',
            'min_amount'   => 10000.00,
            'is_active'    => 1
        ]);
        
        $stepModel->insert([
            'workflow_id'      => $wfId,
            'step_no'          => 1,
            'step_name'        => 'Manager Approval',
            'approver_type'    => 'user',
            'approver_user_id' => 888 // The Boss
        ]);
        
        CLI::write("1. Created Workflow ID $wfId for Branch 99, Min $10k", 'yellow');

        // 2. Mock a PO submission from an employee ($15k total)
        $_SESSION['is_logged_in'] = true;
        $_SESSION['branch_id'] = 99;
        
        $poId = $poModel->insert([
            'tenant_id'    => 1,
            'branch_id'    => 99,
            'project_id'   => 1,
            'po_number'    => 'PO-TEST-13',
            'title'        => 'Heavy Equip',
            'total_amount' => 15000.00,
            'status'       => 'Pending Approval' // Simulated from Procurement controller
        ]);
        
        $engine = new WorkflowEngine();
        $reqId = $engine->submitRequest('purchase_orders', 'purchase_order', $poId, 777 /* employee */, [], 99, 15000.00);
        
        if ($reqId) {
            CLI::write("  [PASS] Engine intercepted and launched Request ID $reqId", 'green');
        } else {
            CLI::write("  [FAIL] Engine failed to pick up the $15k PO workflow", 'red');
            return;
        }

        // 3. Test The Boss (User 888) approving it
        CLI::write("\n2. Simulating Manager Approval...");
        $engine->processAction($reqId, 888, 'approved', 'Looks good');
        
        $finalReq = $reqModel->find($reqId);
        $finalPo  = $poModel->find($poId);
        
        CLI::write("DEBUG: finalReq status = " . ($finalReq['status'] ?? 'null'), 'yellow');
        CLI::write("DEBUG: finalPo status = " . ($finalPo['status'] ?? 'null'), 'yellow');
        var_dump($finalPo);
        
        if ($finalReq['status'] === 'approved' && $finalPo['status'] === 'approved') {
            CLI::write("  [PASS] Request and Purchase Order finalized to 'approved'.", 'green');
        } else {
            CLI::write("  [FAIL] Failed cascading approval to the PO entity.", 'red');
        }

        // 4. Test missing workflow fallback (Auto-Approve simulated logically)
        CLI::write("\n3. Simulating Low-Value PO ($5k)...");
        $reqIdLow = $engine->submitRequest('purchase_orders', 'purchase_order', $poId, 777 /* employee */, [], 99, 5000.00);
        
        if (!$reqIdLow) {
             CLI::write("  [PASS] Low value PO correctly bypassed workflow engine.", 'green');
        } else {
             CLI::write("  [FAIL] Low value PO erroneously trapped by workflow.", 'red');
        }

        // Cleanup
        $wfModel->delete($wfId, true);
        $stepModel->where('workflow_id', $wfId)->delete();
        $poModel->delete($poId, true);
        $reqModel->delete($reqId, true);
        \Config\Database::connect()->table('fs_approval_logs')->where('request_id', $reqId)->delete();
        
        CLI::write("\nPhase 13 tests completed.", 'green');
    }
}
