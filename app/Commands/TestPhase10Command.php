<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\ProjectInvoiceModel;
use App\Models\ProjectExpenseModel;
use App\Models\PayRunModel;
use App\Services\FinancialReportingEngine;

class TestPhase10Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase10';
    protected $description = 'Verifies Phase 10 Financial Management & Reporting and ABAC scoping.';

    public function run(array $params)
    {
        CLI::write("\n=== Testing Phase 10 (Financial Management & Reporting) ===", 'yellow');

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

        // 1. DATA ENFORCEMENT & SEEDING - INVOICES & EXPENSES
        CLI::write("1. Verifying Financial Data Insertion via ErpModel...", 'white');
        
        $invModel = new ProjectInvoiceModel();
        $invId = $invModel->insert([
            'tenant_id'      => $mockTenantId,
            'branch_id'      => $mockBranchId,
            'project_id'     => $project['id'],
            'invoice_number' => 'TEST-INC-1',
            'direction'      => 'income',
            'total_amount'   => 50000.00,
            'invoice_date'   => '2026-03-01',
            'status'         => 'paid' // Optional depending on schema status rules
        ]);

        if ($invId) {
             CLI::write(" ✓ Seeded dummy Income Invoice ($50,000) (ID: $invId)", 'green');
        } else {
             CLI::error(" ✗ Failed to seed Income Invoice: " . json_encode($invModel->errors()));
        }

        $expModel = new ProjectExpenseModel();
        $expId = $expModel->insert([
            'tenant_id'      => $mockTenantId,
            'branch_id'      => $mockBranchId,
            'project_id'     => $project['id'],
            'category'       => 'Materials',
            'amount'         => 12000.00,
            'expense_date'   => '2026-03-02',
            'status'         => 'approved'
        ]);

        if ($expId) {
             CLI::write(" ✓ Seeded dummy Approved Expense ($12,000) (ID: $expId)", 'green');
        } else {
             CLI::error(" ✗ Failed to seed Expense: " . json_encode($expModel->errors()));
        }

        // 2. RUN REPORTING ENGINE
        CLI::write("\n2. Triggering FinancialReportingEngine for P&L...", 'white');
        $engine = new FinancialReportingEngine();
        $pnl = $engine->generateBranchPnL($mockBranchId);

        CLI::write(print_r($pnl, true));

        if ($pnl['revenue']['invoiced_income'] >= 50000.00 && $pnl['costs']['project_expenses'] >= 12000.00) {
            CLI::write(" ✓ P&L correctly captured the newly seeded ledger items.", 'green');
        } else {
            CLI::error(" ✗ P&L failed to capture new items accurately.");
        }

        // 3. ABAC LEAKAGE TEST
        CLI::write("\n3. Testing RBAC/ABAC isolation against foreign branches...", 'white');
        $foreignPnl = $engine->generateBranchPnL(9999);
        if ($foreignPnl['revenue']['invoiced_income'] == 0 && $foreignPnl['costs']['project_expenses'] == 0) {
             CLI::write(" ✓ Passed: Cross-branch query securely isolated. No ghost ledgers leaked for Branch 9999.", 'green');
        } else {
             CLI::error(" ✗ Data Leak: Foreign branch returned financial numbers.");
        }

        CLI::write("\n=== Phase 10 Tests Complete ===\n", 'yellow');
    }
}
