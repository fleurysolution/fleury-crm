<?php

namespace App\Services;

use App\Models\BOQItemModel;
use App\Models\TimesheetEntryModel;
use App\Models\ProjectExpenseModel;
use App\Models\PurchaseOrderModel;
use App\Models\FsUserModel;

/**
 * ProjectControlService
 * Provides specialized calculations for Project Production & Control.
 */
class ProjectControlService
{
    /**
     * Calculate total labor cost for a project based on timesheet hours and user rates.
     */
    public function getLaborCost(int $projectId): float
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT SUM(te.hours * COALESCE(u.hourly_rate, 0)) AS total_labor_cost
            FROM timesheet_entries te
            JOIN timesheets t ON t.id = te.timesheet_id
            JOIN fs_users u ON u.id = t.user_id
            WHERE te.project_id = ?
        ", [$projectId]);

        return (float)($query->getRow()->total_labor_cost ?? 0);
    }

    /**
     * Calculate Earned Value based on BOQ item progress.
     */
    public function getEarnedValue(int $projectId): float
    {
        $boqModel = new BOQItemModel();
        $items = $boqModel->where('project_id', $projectId)
                         ->where('is_section', 0)
                         ->where('deleted_at IS NULL')
                         ->findAll();

        $totalEV = 0;
        foreach ($items as $item) {
            // Percent complete derived from quantity done vs planned
            $percent = $item['quantity'] > 0 ? ($item['actual_qty'] / $item['quantity']) : 0;
            // Cap at 100% for EV calculations
            $percent = min(1, $percent);
            $totalEV += ($percent * $item['total_amount']);
        }

        return (float)$totalEV;
    }

    /**
     * Calculate Productivity Index for high-level health tracking.
     * CPI = EV / Actual Cost
     */
    public function getPerformanceMetrics(int $projectId): array
    {
        $ev = $this->getEarnedValue($projectId);
        
        // Actual Cost = Labor + Approved Expenses + Invoiced Subcontracts
        $labor = $this->getLaborCost($projectId);
        
        $expenseModel = new ProjectExpenseModel();
        $expenses = $expenseModel->totalApproved($projectId);

        $db = \Config\Database::connect();
        $invoiceSum = $db->table('project_invoices')
            ->selectSum('total_amount')
            ->where('project_id', $projectId)
            ->where('direction', 'expense')
            ->where('deleted_at IS NULL')
            ->get()->getRow()->total_amount ?? 0;

        $actualCost = $labor + $expenses + (float)$invoiceSum;

        $cpi = $actualCost > 0 ? ($ev / $actualCost) : 1;

        return [
            'earned_value' => $ev,
            'actual_cost'  => $actualCost,
            'labor_portion'=> $labor,
            'cpi'         => round($cpi, 2),
            'status'      => $cpi >= 1 ? 'Healthy' : ($cpi > 0.85 ? 'Watch' : 'Critical')
        ];
    }
}
