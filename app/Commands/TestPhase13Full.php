<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TimesheetModel;
use App\Models\PayAppModel;
use App\Models\EstimateModel;
use App\Models\FsApprovalRequestModel;
use App\Services\WorkflowEngine;

class TestPhase13Full extends BaseCommand
{
    protected $group       = 'Verification';
    protected $name        = 'test:phase13_full';
    protected $description = 'Verifies Phase 13: Full Workflow Integration';

    public function run(array $params)
    {
        CLI::write('Starting Phase 13 Full Verification (Workflow Integration)...', 'blue');

        $tsModel = new TimesheetModel();
        $paModel = new PayAppModel();
        $estModel = new EstimateModel();
        $reqModel = new FsApprovalRequestModel();
        $engine = new WorkflowEngine();

        $branchId = 12;
        $tenantId = 1;
        $userId = 1;

        session()->set([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'user' => ['id' => $userId],
            'is_logged_in' => true,
            'geo_access_permission' => 'branch'
        ]);

        $db = \Config\Database::connect();

        // ABSOLUTE CLEANUP
        $db->query("SET FOREIGN_KEY_CHECKS = 0");
        $db->query("TRUNCATE fs_as_approval_workflow_steps");
        $db->query("TRUNCATE fs_as_approval_workflows");
        $db->query("SET FOREIGN_KEY_CHECKS = 1");

        // DEBUG DUMP
        $res = $db->query("SHOW CREATE TABLE fs_as_approval_workflow_steps")->getRowArray();
        CLI::write("SCHEMA fs_as_approval_workflow_steps:\n" . $res['Create Table'], 'yellow');
        $res = $db->query("SHOW CREATE TABLE fs_as_approval_workflows")->getRowArray();
        CLI::write("SCHEMA fs_as_approval_workflows:\n" . $res['Create Table'], 'yellow');

        $modules = ['timesheets', 'pay_apps', 'estimates'];
        foreach ($modules as $mod) {
            $insertData = [
                'workflow_key' => "WF_{$mod}_{$branchId}",
                'module_key' => $mod,
                'branch_id' => $branchId,
                'name' => "Branch $branchId " . ucfirst($mod) . " Workflow",
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            CLI::write("Inserting workflow for $mod...", 'cyan');
            $db->table('fs_as_approval_workflows')->insert($insertData);
            $wfId = $db->insertID();
            
            CLI::write("  Inserted Workflow ID: $wfId", 'yellow');

            $stepData = [
                'workflow_id' => $wfId,
                'step_no' => 1,
                'step_name' => 'Manager Approval',
                'approver_type' => 'user',
                'approver_user_id' => 999, // Mock manager
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            CLI::write("  Inserting step 1 for Workflow $wfId...", 'cyan');
            $db->table('fs_as_approval_workflow_steps')->insert($stepData);
        }

        CLI::write('Step 1: Generated workflows for Branch 12.', 'green');

        // 2. Test Timesheet Workflow
        CLI::write('Step 2: Testing Timesheet Workflow...', 'cyan');
        $tsId = $tsModel->insert([
            'user_id' => $userId,
            'week_start' => '2026-03-02',
            'status' => 'draft',
            'branch_id' => $branchId,
            'tenant_id' => $tenantId
        ]);
        
        // Mock controller logic
        $reqId = $engine->submitRequest('timesheets', 'timesheet', $tsId, $userId, [], $branchId, 40.0);
        if ($reqId) {
            CLI::write("Timesheet Request ID $reqId created.", 'green');
            $engine->processAction($reqId, 999, 'approved', 'Verified');
            $ts = $tsModel->find($tsId);
            if ($ts['status'] === 'approved') {
                CLI::write('SUCCESS: Timesheet workflow cascading works.', 'green');
            } else {
                CLI::write("FAILURE: Timesheet status is {$ts['status']}", 'red');
            }
        }

        // 3. Test Pay App Workflow
        CLI::write('Step 3: Testing Pay App Workflow...', 'cyan');
        $paId = $paModel->insert([
            'project_id' => 1,
            'application_no' => 99,
            'status' => 'Draft',
            'branch_id' => $branchId,
            'tenant_id' => $tenantId
        ]);
        
        $reqId = $engine->submitRequest('pay_apps', 'pay_app', $paId, $userId, [], $branchId, 5000.0);
        if ($reqId) {
            CLI::write("Pay App Request ID $reqId created.", 'green');
            $engine->processAction($reqId, 999, 'approved', 'Paid');
            $pa = $paModel->find($paId);
            if ($pa['status'] === 'Approved') {
                CLI::write('SUCCESS: Pay App workflow cascading works.', 'green');
            } else {
                CLI::write("FAILURE: Pay App status is {$pa['status']}", 'red');
            }
        }

        // 4. Test Estimate Workflow
        CLI::write('Step 4: Testing Estimate Workflow...', 'cyan');
        $estId = $estModel->insert([
            'client_id' => 1,
            'status' => 'draft',
            'branch_id' => $branchId,
            'tenant_id' => $tenantId,
            'estimate_date' => date('Y-m-d'),
            'valid_until' => date('Y-m-d', strtotime('+30 days'))
        ]);
        
        $reqId = $engine->submitRequest('estimates', 'estimate', $estId, $userId, [], $branchId, 10000.0);
        if ($reqId) {
            CLI::write("Estimate Request ID $reqId created.", 'green');
            $engine->processAction($reqId, 999, 'rejected', 'Too expensive');
            $est = $estModel->find($estId);
            if ($est['status'] === 'declined') {
                CLI::write('SUCCESS: Estimate rejection cascading works.', 'green');
            } else {
                CLI::write("FAILURE: Estimate status is {$est['status']}", 'red');
            }
        }

        CLI::write('Phase 13 Full Verification Complete!', 'green');
    }
}
