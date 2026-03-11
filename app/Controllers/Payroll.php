<?php

namespace App\Controllers;

use App\Models\PayrollProfileModel;
use App\Models\TaxProfileModel;
use App\Models\PayRunModel;
use App\Services\PayrollEngine;

class Payroll extends BaseAppController
{
    /**
     * GET /payroll
     * Renders the HR Payroll Dashboard.
     */
    public function index()
    {
        $data['title'] = 'Payroll Dashboard';

        $data['runs'] = (new PayRunModel())->orderBy('created_at', 'DESC')->findAll();
        $data['taxProfiles'] = (new TaxProfileModel())->findAll();
        $data['payrollProfiles'] = (new PayrollProfileModel())->findAll();

        return view('payroll/dashboard', $data);
    }

    /**
     * POST /payroll/profiles
     * Generates a branch-level rule for payroll cycles.
     */
    public function storeProfile(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tenantId = session('tenant_id');
        $branchId = session('branch_id');

        $data = [
            'tenant_id'        => $tenantId,
            'branch_id'        => $branchId,
            'name'             => $this->request->getPost('name'),
            'pay_period'       => $this->request->getPost('pay_period') ?: 'Bi-Weekly',
            'overtime_rule_id' => $this->request->getPost('overtime_rule_id') ?: null
        ];

        (new PayrollProfileModel())->insert($data);
        return redirect()->back()->with('success', 'Crew Payroll Profile Created.');
    }

    /**
     * POST /payroll/tax-profiles
     * Configures a localized tax tier.
     */
    public function storeTaxProfile(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tenantId = session('tenant_id');
        $branchId = session('branch_id');

        $data = [
            'tenant_id'   => $tenantId,
            'branch_id'   => $branchId,
            'name'        => $this->request->getPost('name'),
            'tax_rate'    => $this->request->getPost('tax_rate'),
            'region_code' => $this->request->getPost('region_code')
        ];

        (new TaxProfileModel())->insert($data);
        return redirect()->back()->with('success', 'Tax Profile Created.');
    }

    /**
     * POST /payroll/runs/generate
     * Triggers the Payroll Engine to digest unprocessed timesheets into PaySlips.
     */
    public function generateRun(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tenantId = session('tenant_id');
        $branchId = session('branch_id');
        $userId   = session('user_id');

        $periodStart = $this->request->getPost('period_start');
        $periodEnd   = $this->request->getPost('period_end');

        try {
            $engine = new PayrollEngine();
            $payRunId = $engine->generatePayRun(
                (int)$tenantId,
                (int)$branchId,
                $periodStart,
                $periodEnd,
                (int)$userId
            );

            if ($payRunId) {
                return redirect()->back()->with('success', "Crew Pay Run #$payRunId generated successfully.");
            } else {
                return redirect()->back()->with('info', "No approved, unprocessed timesheets found for this period.");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Payroll Engine Failed: " . $e->getMessage());
        }
    }

    /**
     * POST /payroll/runs/:id/approve
     * Submits the Draft PayRun for actual distribution/ledger locks.
     */
    public function approveRun(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $model = new PayRunModel();
        // Since PayRunModel extends ErpModel, it automatically scoped this lookup against branch leakage.
        $run = $model->find($id);

        if (!$run) {
            return redirect()->back()->with('error', 'Pay Run not found or out of division scope.');
        }

        $model->update($id, [
            'status'      => 'Approved',
            'approved_by' => session('user_id')
        ]);

        return redirect()->back()->with('success', 'Crew Pay Run Approved.');
    }
}
