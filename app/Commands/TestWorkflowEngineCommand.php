<?php

namespace App\Commands;

use App\Services\WorkflowEngine;
use CodeIgniter\CLI\CLI;

class TestWorkflowEngineCommand extends \CodeIgniter\CLI\BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:workflow';
    protected $description = 'Tests the Workflow Engine';

    public function run(array $params)
    {
        CLI::write("Testing WorkflowEngine...", 'yellow');
        
        $engine = new WorkflowEngine();
        $db = \Config\Database::connect();
        
        // 1. Setup a fake workflow for Purchase Orders (module: procurement, branch: 1, amount > 50000)
        $db->table('fs_as_approval_workflows')->insert([
            'workflow_key' => 'PO_HIGH_VALUE_B1',
            'module_key' => 'procurement',
            'branch_id' => 1,
            'name' => 'High Value PO Branch 1',
            'min_amount' => 50000.00,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $workflowId = $db->insertID();

        // 2. Add a Step
        $db->table('fs_as_approval_workflow_steps')->insert([
            'workflow_id' => $workflowId,
            'step_no' => 1,
            'step_name' => 'Regional Manager Approval',
            'approver_type' => 'user',
            'approver_user_id' => 99, // Fake User
            'min_approvals' => 1,
            'is_mandatory' => 1
        ]);

        // 3. Setup Delegation (User 99 delegates to User 100)
        $db->table('fs_approval_delegations')->insert([
            'delegator_user_id' => 99,
            'delegatee_user_id' => 100,
            'start_date' => date('Y-m-d', strtotime('-1 day')),
            'end_date' => date('Y-m-d', strtotime('+5 days')),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 4. Test resolution
        CLI::write("Resolving workflow for PO, amount 75000, branch 1...", 'white');
        $resolved = $engine->resolveWorkflow('procurement', 1, 75000.00);
        
        if ($resolved && $resolved['id'] == $workflowId) {
            CLI::write("SUCCESS: Resolved correct workflow.", 'green');
        } else {
            CLI::error("FAIL: Did not resolve correct workflow.");
        }

        // 5. Test Delegation
        CLI::write("Checking delegation for User 99...", 'white');
        $delegatee = $engine->resolveDelegatee(99);
        if ($delegatee === 100) {
            CLI::write("SUCCESS: Delegation correctly routed to User 100.", 'green');
        } else {
            CLI::error("FAIL: Delegation routed to $delegatee instead of 100.");
        }

        // 6. Test Logging
        CLI::write("Testing logging capability...", 'white');
        $engine->logAction(1, 1, 1, 'approved', 'Looks good to me.');
        $logCount = $db->table('fs_approval_logs')->countAllResults();
        
        if ($logCount > 0) {
            CLI::write("SUCCESS: Write to fs_approval_logs succeeded.", 'green');
        } else {
            CLI::error("FAIL: Did not write to fs_approval_logs.");
        }

        // Cleanup
        $db->table('fs_approval_logs')->truncate();
        $db->table('fs_approval_delegations')->truncate();
        $db->table('fs_as_approval_workflow_steps')->where('workflow_id', $workflowId)->delete();
        $db->table('fs_as_approval_workflows')->where('id', $workflowId)->delete();
        
        CLI::write("Cleanup complete.", 'yellow');
    }
}
