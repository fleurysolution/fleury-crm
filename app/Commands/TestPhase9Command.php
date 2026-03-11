<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\UserModel;
use App\Models\TimesheetModel;
use App\Models\PayrollProfileModel;
use App\Models\TaxProfileModel;
use App\Models\PayRunModel;
use App\Models\PaySlipModel;
use App\Services\PayrollEngine;

class TestPhase9Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase9';
    protected $description = 'Verifies Phase 9 Workforce & Payroll Engine and ABAC scoping.';

    public function run(array $params)
    {
        CLI::write("\n=== Testing Phase 9 (Workforce & Payroll Engine) ===", 'yellow');

        // Locate a mock project to derive tenant/branch
        $projModel = new ProjectModel();
        $project = $projModel->first();
        if (!$project) {
            CLI::error("No active project found. Please seed data first.");
            return;
        }

        $mockBranchId = (int)$project['branch_id'] ?: 17;
        $mockTenantId = (int)$project['tenant_id'] ?: 9;

        // Establish Mock Session to simulate ABAC filters
        $_SESSION['user_id']   = 999;
        $_SESSION['branch_id'] = $mockBranchId;
        $_SESSION['tenant_id'] = $mockTenantId;
        $_SESSION['role_id']   = 3; // Finance/HR 
        $_SESSION['geo_access_permission'] = 'branch';
        $_SESSION['user_roles'] = ['finance_manager'];
        $_SESSION['is_logged_in'] = true;

        CLI::write("Using Branch ID: {$mockBranchId} (Tenant: $mockTenantId)\n", 'cyan');

        // ---------------------------------------------------------------------
        // 1. DATA ENFORCEMENT & SETUP
        // ---------------------------------------------------------------------
        CLI::write("1. Testing Payroll & Tax Profile Enforcement...", 'white');
        
        $pModel = new PayrollProfileModel();
        
        try {
            // Missing branch_id test via ErpModel inheritance
            $_SESSION['branch_id'] = null;
            $pModel->insert([
                'tenant_id' => $mockTenantId,
                'name'      => 'Bi-Weekly standard cross-branch attempt',
                'pay_period'=> 'Bi-Weekly'
            ]);
            CLI::error(" ✗ PayrollProfileModel failed to throw ErpModel branch_id exception.");
        } catch (\Exception $e) {
            CLI::write(" ✓ PayrollProfileModel correctly blocked missing branch_id.", 'green');
        }

        $_SESSION['branch_id'] = $mockBranchId;

        // Seed Tax Profile
        $tModel = new TaxProfileModel();
        $taxId = $tModel->insert([
            'tenant_id'   => $mockTenantId,
            'branch_id'   => $mockBranchId,
            'name'        => 'Standard Local Tax',
            'tax_rate'    => 12.50,
            'region_code' => 'LOCAL-100'
        ]);
        CLI::write(" ✓ Seeded Tax Profile (ID: $taxId)", 'green');

        // Ensure we have a dummy user in fs_users mapped to this tax_profile_id
        $db = \Config\Database::connect();
        $userId = 1001;
        $db->table('fs_users')->ignore(true)->insert([
            'id'             => $userId,
            'first_name'     => 'Worker',
            'last_name'      => 'Test',
            'email'          => 'worker_test@bpms.com',
            'tax_profile_id' => $taxId,
            'employment_type'=> 'Full-Time'
        ]);
        CLI::write(" ✓ Seeded/Verified Test Worker (ID: $userId)", 'green');

        // ---------------------------------------------------------------------
        // 2. TIMESHEET INGESTION & PROCESSING
        // ---------------------------------------------------------------------
        CLI::write("\n2. Seeding Timesheets & Triggering Engine...", 'white');

        $tsModel = new TimesheetModel();
        $tsId = $tsModel->insert([
            'tenant_id'      => $mockTenantId,
            'branch_id'      => $mockBranchId,
            'user_id'        => $userId,
            'week_start'     => '2026-03-01',
            'status'         => 'approved',
            'payroll_status' => 'Unprocessed',
            'notes'          => 'Phase 9 Auto-Test'
        ]);

        // Insert fake hours for the timesheet
        $db->table('timesheet_entries')->insert([
            'timesheet_id' => $tsId,
            'project_id'   => $project['id'],
            'entry_date'   => '2026-03-02',
            'hours'        => 40.00
        ]);

        CLI::write(" ✓ Seeded dummy timesheet (ID: $tsId) with 40 hrs.", 'green');

        $engine = new PayrollEngine();
        try {
            $payRunId = $engine->generatePayRun(
                $mockTenantId,
                $mockBranchId,
                '2026-03-01',
                '2026-03-07',
                $_SESSION['user_id']
            );

            if ($payRunId) {
                CLI::write(" ✓ PayrollEngine successfully digested timesheets into Pay Run (ID: $payRunId)", 'green');

                // Verify the slip
                $slipModel = new PaySlipModel();
                $slips = $slipModel->where('pay_run_id', $payRunId)->findAll();
                
                if (count($slips) > 0) {
                    $slip = $slips[0];
                    CLI::write(" ✓ Generated matching PaySlip -> Gross: {$slip['gross_pay']}, Taxes: {$slip['taxes_withheld']}, Net: {$slip['net_pay']}", 'green');
                } else {
                    CLI::error(" ✗ PaySlips array is empty!");
                }

                // Verify timesheet status mutated
                $mutatedTs = $tsModel->find($tsId);
                if ($mutatedTs['payroll_status'] === 'Processed' && $mutatedTs['pay_run_id'] == $payRunId) {
                    CLI::write(" ✓ Timesheet accurately tagged as Processed and linked to PayRun.", 'green');
                } else {
                    CLI::error(" ✗ Timesheet was NOT correctly tagged after PayloadEngine run.");
                }

            } else {
                CLI::error(" ✗ PayrollEngine failed to generate a PayRun.");
            }
        } catch (\Exception $e) {
            CLI::error(" ✗ PayrollEngine execution threw Exception: " . $e->getMessage());
        }

        // ---------------------------------------------------------------------
        // 3. ABAC READ FILTERING
        // ---------------------------------------------------------------------
        CLI::write("\n3. Testing RBAC/ABAC read filtering on Pay Runs...", 'white');
        
        try {
            $runModel = new PayRunModel();
            $runs = $runModel->findAll();
            $leak = false;
            foreach ($runs as $r) {
                if ($r['branch_id'] != $mockBranchId) {
                    $leak = true;
                }
            }
            if ($leak) {
                 CLI::error(" ✗ Data Leak: Found PayRuns belonging to outside branches.");
            } else {
                 CLI::write(" ✓ Passed: Successfully read branch-scoped PayRuns securely isolated to user's branch.", 'green');
            }
        } catch (\Exception $e) {
            CLI::error(" ✗ ABAC read test threw Exception: " . $e->getMessage());
        }

        CLI::write("\n=== Phase 9 Tests Complete ===\n", 'yellow');
    }
}
