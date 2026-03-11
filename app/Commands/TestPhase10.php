<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectInvoiceModel;
use App\Models\ProjectExpenseModel;
use App\Models\PayRunModel;
use App\Models\PaySlipModel;
use App\Services\FinancialReportingEngine;

class TestPhase10 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase10';
    protected $description = 'Verifies Phase 10 Financial isolation and P&L Engine';

    public function run(array $params)
    {
        CLI::write("Testing Phase 10: Financial P&L isolation", 'green');
        
        $invoiceModel = new ProjectInvoiceModel();
        $expenseModel = new ProjectExpenseModel();
        $runModel     = new PayRunModel();
        $slipModel    = new PaySlipModel();
        $engine       = new FinancialReportingEngine();
        $db           = \Config\Database::connect();

        // Branch 12 Setup
        CLI::write("1. Seeding Financial Data for Branch 12...", 'yellow');
        
        $db->table('project_invoices')->where('party_name', 'Test Client B12')->delete();
        $db->table('project_expenses')->where('description', 'Test Expense B12')->delete();
        $db->table('fs_pay_slips')->where('gross_pay', 555.00)->delete();
        $db->table('fs_pay_runs')->where('branch_id', 12)->where('status', 'Approved')->delete();

        // 1a. Revenue
        $invoiceModel->insert([
            'tenant_id'      => 1,
            'branch_id'      => 12,
            'direction'      => 'income',
            'party_name'     => 'Test Client B12',
            'total_amount'   => 10000.00,
            'invoice_date'   => '2026-03-01',
            'status'         => 'paid'
        ]);

        // 1b. Direct Expense
        $expenseModel->insert([
            'tenant_id'    => 1,
            'branch_id'    => 12,
            'description'  => 'Test Expense B12',
            'amount'       => 2000.00,
            'expense_date' => '2026-03-02',
            'status'       => 'approved'
        ]);

        // 1c. Payroll Expense
        $payRunId = $runModel->insert([
            'tenant_id'        => 1,
            'branch_id'        => 12,
            'pay_period_start' => '2026-03-01',
            'pay_period_end'   => '2026-03-07',
            'status'           => 'Approved'
        ]);
        $slipModel->insert([
            'pay_run_id' => $payRunId,
            'user_id'    => 1,
            'gross_pay'  => 3000.00,
            'net_pay'    => 2400.00
        ]);

        CLI::write("2. Verifying P&L for Branch 12...", 'yellow');
        $pnl = $engine->generateBranchPnL(12);

        if ($pnl['revenue']['invoiced_income'] == 10000 && 
            $pnl['costs']['project_expenses'] == 2000 && 
            $pnl['costs']['payroll_expenses'] == 3000 &&
            $pnl['profitability']['net_profit'] == 5000) {
            CLI::write("  [PASS] Branch 12 P&L Calculation accurate (Net Profit: 5000).", 'green');
        } else {
            CLI::write("  [FAIL] Branch 12 P&L Calculation error.", 'red');
            CLI::print_r($pnl);
        }

        CLI::write("\n3. Testing Cross-Branch Isolation (Branch 24)...", 'yellow');
        $pnlCross = $engine->generateBranchPnL(24);
        
        if ($pnlCross['revenue']['invoiced_income'] == 0 && $pnlCross['profitability']['net_profit'] == 0) {
            CLI::write("  [PASS] Branch 24 P&L is empty and isolated from Branch 12.", 'green');
        } else {
            CLI::write("  [FAIL] ABAC Leak: Branch 24 result contains Branch 12 data.", 'red');
            CLI::print_r($pnlCross);
        }

        // Cleanup
        $db->table('project_invoices')->where('party_name', 'Test Client B12')->delete();
        $db->table('project_expenses')->where('description', 'Test Expense B12')->delete();
        $db->table('fs_pay_slips')->where('pay_run_id', $payRunId)->delete();
        $db->table('fs_pay_runs')->where('id', $payRunId)->delete();

        CLI::write("\nPhase 10 tests completed.", 'green');
    }
}
