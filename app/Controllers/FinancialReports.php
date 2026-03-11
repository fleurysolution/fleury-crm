<?php

namespace App\Controllers;

use App\Services\FinancialReportingEngine;

class FinancialReports extends BaseAppController
{
    /**
     * GET /reports/financial/pnl
     * Generates a structural Profit and Loss array based on ABAC branch constraints.
     */
    public function pnl()
    {
        $branchId = session('branch_id');
        if (!$branchId) {
            return redirect()->back()->with('error', 'Branch ID required in session for ERP scoping.');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $engine = new FinancialReportingEngine();
        try {
            $data['pnlData'] = $engine->generateBranchPnL((int)$branchId, $startDate, $endDate);
            $data['title'] = 'Profit & Loss Statement';
            $data['startDate'] = $startDate;
            $data['endDate'] = $endDate;

            return view('finance/reports_pnl', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
