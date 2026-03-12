<?php

namespace App\Models;

use CodeIgniter\Model;

class BudgetModel extends Model
{
    public function getProjectFinancials(int $projectId, ?int $tenantId): array
    {
        $db = \Config\Database::connect();

        // 1. Original Budget (Direct Costs)
        $itemsSum = $db->table('project_budget_items')
            ->selectSum('total_cost')
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->where('deleted_at IS NULL')
            ->get()->getRow();
        
        $project = $db->table('projects')->where('id', $projectId)->get()->getRowArray();
        
        $directBudget = ($itemsSum->total_cost > 0) ? (float)$itemsSum->total_cost : (float)($project['budget'] ?? 0);

        // 1.1 Estimated General Conditions (GCs) from Approved Estimates
        $gcSum = $db->table('project_estimate_gcs')
            ->selectSum('amount')
            ->join('project_estimates', 'project_estimates.id = project_estimate_gcs.estimate_id')
            ->where('project_estimates.project_id', $projectId)
            ->where('project_estimates.status', 'Approved')
            ->get()->getRow();
        $totalGCs = (float)($gcSum->amount ?? 0);

        $originalBudget = $directBudget + $totalGCs;

        // 2. Approved Change Orders
        $approvedCOs = $db->table('change_orders')
            ->selectSum('amount')
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->get()->getRow();
        $totalCOs = (float)($approvedCOs->amount ?? 0);

        // 3. Actual Expenses (Approved)
        $actualExpenses = $db->table('project_expenses')
            ->selectSum('amount')
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->where('deleted_at IS NULL')
            ->get()->getRow();
        $totalExpenses = (float)($actualExpenses->amount ?? 0);

        // 4. Vendor Invoices (Expense direction)
        $actualInvoices = $db->table('project_invoices')
            ->selectSum('total_amount')
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->where('direction', 'expense')
            ->where('deleted_at IS NULL')
            ->get()->getRow();
        $totalInvoices = (float)($actualInvoices->total_amount ?? 0);

        // 5. Total Actual Spend
        $totalActual = $totalExpenses + $totalInvoices;

        // 6. Committed (Purchase Orders - Approved)
        $purchaseOrders = $db->table('project_purchase_orders')
            ->selectSum('total_amount')
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->get()->getRow();
        $totalCommitted = (float)($purchaseOrders->total_amount ?? 0);

        $revisedBudget = $originalBudget + $totalCOs;
        $variance = $revisedBudget - $totalActual;
        
        return [
            'original_budget' => $originalBudget,
            'approved_cos'    => $totalCOs,
            'revised_budget'  => $revisedBudget,
            'actual_spend'    => $totalActual,
            'committed'       => $totalCommitted,
            'variance'        => $variance,
            'percent_spent'   => $revisedBudget > 0 ? round(($totalActual / $revisedBudget) * 100, 1) : 0,
            'currency'        => $project['currency'] ?? 'USD'
        ];
    }
}
