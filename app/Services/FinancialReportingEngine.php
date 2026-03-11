<?php

namespace App\Services;

use App\Models\ProjectInvoiceModel;
use App\Models\ProjectExpenseModel;
use App\Models\PayRunModel;
use App\Models\PaySlipModel;

class FinancialReportingEngine
{
    /**
     * Generates a high-level Profit & Loss statement for a specific branch.
     * 
     * @param int $branchId The branch ID to scope reporting to.
     * @param string|null $startDate Filter from date
     * @param string|null $endDate Filter to date
     * @return array
     */
    public function generateBranchPnL(int $branchId, ?string $startDate = null, ?string $endDate = null): array
    {
        // 1. Calculate Income (Invoices facing outward)
        $invoiceModel = new ProjectInvoiceModel();
        // ErpModel will automatically scope these queries to session branch_id, but the Engine 
        // provides explicit parameterized control for aggregation (e.g. if an Admin requests it).
        // To ensure the Engine can run without strictly binding to the session, we manually enforce where.
        
        $invoicesQuery = $invoiceModel->where('branch_id', $branchId)->where('direction', 'income');
        if ($startDate) $invoicesQuery = $invoicesQuery->where('invoice_date >=', $startDate);
        if ($endDate) $invoicesQuery = $invoicesQuery->where('invoice_date <=', $endDate);
        
        $incomeInvoices = $invoicesQuery->findAll();
        $totalIncome = 0.00;
        foreach ($incomeInvoices as $inv) {
            $totalIncome += (float)$inv['total_amount'];
        }

        // 2. Calculate Expenses (Direct Project Expenses + Subcontractor Invoices)
        $totalExpenses = 0.00;
        
        // 2a. Direct Project Expenses
        $expenseModel = new ProjectExpenseModel();
        $expensesQuery = $expenseModel->where('branch_id', $branchId)->where('status', 'approved');
        if ($startDate) $expensesQuery = $expensesQuery->where('expense_date >=', $startDate);
        if ($endDate) $expensesQuery = $expensesQuery->where('expense_date <=', $endDate);
        
        $expenses = $expensesQuery->findAll();
        foreach ($expenses as $exp) {
            $totalExpenses += (float)$exp['amount'];
        }

        // 2b. Outward Invoices (Vendor / Pay Apps)
        $vendorInvoicesQuery = $invoiceModel->where('branch_id', $branchId)->where('direction', 'expense');
        if ($startDate) $vendorInvoicesQuery = $vendorInvoicesQuery->where('invoice_date >=', $startDate);
        if ($endDate) $vendorInvoicesQuery = $vendorInvoicesQuery->where('invoice_date <=', $endDate);
        
        $vendorInvoices = $vendorInvoicesQuery->findAll();
        foreach ($vendorInvoices as $vInv) {
            $totalExpenses += (float)$vInv['total_amount'];
        }

        // 3. Calculate Payroll Costs
        $totalPayroll = 0.00;
        $runModel = new PayRunModel();
        $runsQuery = $runModel->where('branch_id', $branchId)->where('status', 'Approved'); // Standardize status?
        
        // For Payroll, we filter by the start/end of the pay run explicitly
        if ($startDate) $runsQuery = $runsQuery->where('pay_period_end >=', $startDate);
        if ($endDate) $runsQuery = $runsQuery->where('pay_period_start <=', $endDate);
        
        $runs = $runsQuery->findAll();
        if (!empty($runs)) {
            $runIds = array_column($runs, 'id');
            $slipModel = new PaySlipModel();
            $slips = $slipModel->whereIn('pay_run_id', $runIds)->findAll();
            foreach ($slips as $slip) {
                $totalPayroll += (float)$slip['gross_pay']; // Company's liability is gross
            }
        }

        $grossProfit = $totalIncome - $totalExpenses;
        $netProfit   = $grossProfit - $totalPayroll;

        return [
            'branch_id'      => $branchId,
            'period_start'   => $startDate ?: 'All Time',
            'period_end'     => $endDate ?: 'All Time',
            'revenue'        => [
                'invoiced_income' => $totalIncome
            ],
            'costs'          => [
                'project_expenses' => $totalExpenses,
                'payroll_expenses' => $totalPayroll,
                'total_costs'      => $totalExpenses + $totalPayroll
            ],
            'profitability'  => [
                'gross_profit' => $grossProfit,
                'net_profit'   => $netProfit,
                'margin_pct'   => $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0
            ]
        ];
    }
}
