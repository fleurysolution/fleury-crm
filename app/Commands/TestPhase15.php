<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectExpenseModel;
use App\Services\WorkflowEngine;

class TestPhase15 extends BaseCommand
{
    protected $group       = 'Test';
    protected $name        = 'test:phase15';
    protected $description = 'Verify Project Expense Workflow Integration';

    public function run(array $params)
    {
        CLI::write("Starting Phase 15 Verification (Expenses Workflow)...", 'cyan');

        $db = \Config\Database::connect();
        
        // 1. Setup Session Mocks
        session()->set([
            'tenant_id' => 1,
            'branch_id' => 12, // Using same branch as Phase 13 tests
            'user_id'   => 1,
            'role_slug' => 'admin',
            'is_logged_in' => true
        ]);

        // 2. Ensure Workflow exists for expenses in Branch 12
        $db->table('fs_as_approval_workflows')->where('workflow_key', 'workflow_expenses_b12')->delete();
        $db->table('fs_as_approval_workflows')->insert([
            'workflow_key' => 'workflow_expenses_b12',
            'module_key'   => 'expenses',
            'branch_id'    => 12,
            'entity_key'   => 'project_expense',
            'name'         => 'Expense Approval Branch 12',
            'min_amount'   => 0,
            'max_amount'   => 1000000,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        $wId = $db->insertID();
        
        $db->table('fs_as_approval_workflow_steps')->where('workflow_id', $wId)->delete();
        $db->table('fs_as_approval_workflow_steps')->insert([
            'workflow_id' => $wId,
            'step_no'     => 1,
            'step_name'   => 'Manager Review',
            'approver_type' => 'user',
            'approver_user_id' => 1,
            'min_approvals' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $eModel = new ProjectExpenseModel();

        // 3. Test Submission
        CLI::write("Step 1: Testing Expense Submission...", 'yellow');
        $expenseId = $eModel->insert([
            'project_id'  => 10,
            'tenant_id'   => 1,
            'branch_id'   => 12,
            'category'    => 'Equipment',
            'description' => 'Generator Rental',
            'amount'      => 500.00,
            'status'      => 'submitted',
            'expense_date'=> date('Y-m-d'),
            'submitted_by'=> 1
        ]);

        $workflow = new WorkflowEngine();
        $reqId = $workflow->submitRequest('expenses', 'project_expense', $expenseId, 1, [], 12, 500.00);

        if (!$reqId) {
            CLI::error("FAILURE: Workflow request not created for expense.");
            return;
        }
        CLI::write("SUCCESS: Expense request ID $reqId created.", 'green');

        // 4. Test Approval
        CLI::write("Step 2: Testing Expense Approval...", 'yellow');
        $workflow->processAction($reqId, 1, 'approved', "Approved for site use.");
        
        $check = $eModel->find($expenseId);
        if ($check['status'] === 'approved') {
            CLI::write("SUCCESS: Expense status updated to approved.", 'green');
        } else {
            CLI::error("FAILURE: Expense status is " . $check['status']);
        }

        // 5. Test Rejection
        CLI::write("Step 3: Testing Expense Rejection...", 'yellow');
        $expenseId2 = $eModel->insert([
            'project_id'  => 10,
            'tenant_id'   => 1,
            'branch_id'   => 12,
            'category'    => 'Other',
            'description' => 'Pizza Party',
            'amount'      => 100.00,
            'status'      => 'submitted',
            'expense_date'=> date('Y-m-d'),
            'submitted_by'=> 1
        ]);
        $reqId2 = $workflow->submitRequest('expenses', 'project_expense', $expenseId2, 1, [], 12, 100.00);
        $workflow->processAction($reqId2, 1, 'rejected', "Not project related.");
        
        $check2 = $eModel->find($expenseId2);
        if ($check2['status'] === 'rejected') {
            CLI::write("SUCCESS: Expense status updated to rejected.", 'green');
        } else {
            CLI::error("FAILURE: Expense status is " . $check2['status']);
        }

        CLI::write("Phase 15 Verification Complete!", 'cyan');
    }
}
