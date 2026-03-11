<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SiteDiaryModel;
use App\Services\WorkflowEngine;

class TestPhase16 extends BaseCommand
{
    protected $group       = 'Test';
    protected $name        = 'test:phase16';
    protected $description = 'Verify Site Diary Workflow Integration';

    public function run(array $params)
    {
        CLI::write("Starting Phase 16 Verification (Site Diary Workflow)...", 'cyan');

        $db = \Config\Database::connect();
        
        // 1. Setup Session Mocks
        session()->set([
            'tenant_id' => 1,
            'branch_id' => 12,
            'user_id'   => 1,
            'role_slug' => 'admin',
            'is_logged_in' => true
        ]);

        // 2. Ensure Workflow exists for site_diaries in Branch 12
        $db->table('fs_as_approval_workflows')->where('workflow_key', 'workflow_diaries_b12')->delete();
        $db->table('fs_as_approval_workflows')->insert([
            'workflow_key' => 'workflow_diaries_b12',
            'module_key'   => 'site_diaries',
            'branch_id'    => 12,
            'entity_key'   => 'project_site_diary',
            'name'         => 'Site Diary Approval Branch 12',
            'min_amount'   => 0,
            'max_amount'   => 1000000,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        $wId = $db->insertID();
        
        $db->table('fs_as_approval_workflow_steps')->where('workflow_id', $wId)->delete();
        $db->table('fs_as_approval_workflow_steps')->insert([
            'workflow_id' => $wId,
            'step_no'     => 1,
            'step_name'   => 'PM Review',
            'approver_type' => 'user',
            'approver_user_id' => 1,
            'min_approvals' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $sdModel = new SiteDiaryModel();

        // 3. Test Submission
        CLI::write("Step 1: Testing Site Diary Submission...", 'yellow');
        $diaryId = $sdModel->insert([
            'project_id'  => 10,
            'tenant_id'   => 1,
            'branch_id'   => 12,
            'report_date' => date('Y-m-d'),
            'status'      => 'Submitted',
            'created_by'  => 1
        ]);

        $workflow = new WorkflowEngine();
        $reqId = $workflow->submitRequest('site_diaries', 'project_site_diary', $diaryId, 1, [], 12);

        if (!$reqId) {
            CLI::error("FAILURE: Workflow request not created for Site Diary.");
            return;
        }
        CLI::write("SUCCESS: Site Diary request ID $reqId created.", 'green');

        // 4. Test Approval
        CLI::write("Step 2: Testing Site Diary Approval...", 'yellow');
        $workflow->processAction($reqId, 1, 'approved', "Looks good.");
        
        $check = $sdModel->find($diaryId);
        if ($check['status'] === 'Approved') {
            CLI::write("SUCCESS: Site Diary status updated to Approved.", 'green');
        } else {
            CLI::error("FAILURE: Site Diary status is " . $check['status']);
        }

        CLI::write("Phase 16 Verification Complete!", 'cyan');
    }
}
