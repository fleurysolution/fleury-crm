<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TimesheetModel;
use App\Models\TaxProfileModel;
use App\Models\PayRunModel;
use App\Models\PaySlipModel;
use App\Services\PayrollEngine;

class TestPhase9 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase9';
    protected $description = 'Verifies Phase 9 Workforce & Payroll ABAC logic';

    public function run(array $params)
    {
        CLI::write("Testing Phase 9: Workforce & Payroll isolation", 'green');
        
        $tsModel    = new TimesheetModel();
        $taxModel   = new TaxProfileModel();
        $runModel   = new PayRunModel();
        $slipModel  = new PaySlipModel();
        $engine     = new PayrollEngine();
        $db         = \Config\Database::connect();

        // Setup Session for Branch 12
        $_SESSION['is_logged_in'] = true;
        $_SESSION['tenant_id'] = 1; 
        $_SESSION['branch_id'] = 12;
        $_SESSION['user_id'] = 1012;
        $_SESSION['geo_access_permission'] = 'branch';
        $_SESSION['user_roles'] = ['hr_manager'];

        CLI::write("1. Seeding Data for Branch 12 (Cleaning up first)...", 'yellow');
        $db->table('timesheet_entries')->where('timesheet_id', 99912)->delete(); 
        $db->table('fs_users')->where('id', 9912)->delete();
        $db->table('timesheets')->where('user_id', 9912)->delete();
        $taxModel->where('name', 'B12 Standard Tax')->delete();

        // 1. Create Tax Profile
        $taxId = $taxModel->insert([
            'tenant_id' => 1,
            'branch_id' => 12,
            'name'      => 'B12 Standard Tax',
            'tax_rate'  => 20.00
        ]);

        // 2. Mock User with Tax Profile and Hourly Rate
        $db->table('fs_users')->insert([
            'id'             => 9912,
            'tenant_id'      => 1,
            'branch_id'      => 12,
            'first_name'     => 'Worker',
            'last_name'      => 'Twelve',
            'email'          => 'worker12@test.com',
            'tax_profile_id' => $taxId,
            'hourly_rate'    => 50.00
        ]);

        // 3. Create Approved Timesheet
        $tsId = $tsModel->insert([
            'tenant_id'      => 1,
            'branch_id'      => 12,
            'user_id'        => 9912,
            'week_start'     => '2026-03-01',
            'status'         => 'approved',
            'payroll_status' => 'Unprocessed'
        ]);

        // Seed entries for hours via raw SQL or query builder since it's child table
        $db->table('timesheet_entries')->insert([
            'timesheet_id' => $tsId,
            'hours'        => 40.00,
            'entry_date'   => '2026-03-02'
        ]);

        CLI::write("2. Running Payroll Engine for Branch 12...", 'yellow');
        
        $payRunId = $engine->generatePayRun(1, 12, '2026-03-01', '2026-03-07', 1012);
        
        if (!$payRunId) {
            CLI::write("  [FAIL] Payroll Engine returned no PayRun.", 'red');
            return;
        }

        // Verify PaySlip
        $slip = $slipModel->where('pay_run_id', $payRunId)->where('user_id', 9912)->first();
        if ($slip && (float)$slip['gross_pay'] == 2000.00 && (float)$slip['net_pay'] == 1600.00) {
            CLI::write("  [PASS] PaySlip calculation correct (Gross: 2000, Tax 20% -> Net: 1600).", 'green');
        } else {
            CLI::write("  [FAIL] PaySlip calculation error.", 'red');
            CLI::print_r($slip);
        }

        // 3. Cross-Branch Isolation Check (Branch 24)
        CLI::write("\n3. Testing Cross-Branch Scope Block (Branch 24)...", 'yellow');
        $_SESSION['branch_id'] = 24; 
        
        $runCross = $runModel->find($payRunId);
        if (!$runCross) {
            CLI::write("  [PASS] Branch 24 User cannot see Branch 12 Pay Run.", 'green');
        } else {
            CLI::write("  [FAIL] ABAC Leak: Branch 24 user accessed Branch 12 Pay Run.", 'red');
        }

        // Cleanup
        $_SESSION['user_roles'] = ['admin'];
        $db->table('timesheet_entries')->where('timesheet_id', $tsId)->delete();
        $tsModel->delete($tsId, true);
        $db->table('fs_users')->where('id', 9912)->delete();
        $taxModel->delete($taxId, true);
        $runModel->delete($payRunId, true);
        $db->table('fs_pay_slips')->where('pay_run_id', $payRunId)->delete();

        CLI::write("\nPhase 9 tests completed.", 'green');
    }
}
