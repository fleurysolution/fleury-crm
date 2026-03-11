<?php

namespace App\Services;

use App\Models\TimesheetModel;
use App\Models\PayrollProfileModel;
use App\Models\TaxProfileModel;
use App\Models\PayRunModel;
use App\Models\PaySlipModel;

class PayrollEngine
{
    /**
     * Extracts approved, unprocessed timesheets for a given branch
     * and compiles them into a standardized PayRun batch with PaySlips.
     */
    public function generatePayRun(int $tenantId, int $branchId, string $periodStart, string $periodEnd, int $runByUserId): ?int
    {
        $tsModel = new TimesheetModel();
        
        // Since TimesheetModel inherits ErpModel, we manually enforce branch_id restriction
        // to collect all pending timesheets matching the period.
        $timesheets = $tsModel->where('status', 'approved')
                              ->where('payroll_status', 'Unprocessed')
                              ->where('branch_id', $branchId)
                              ->where('week_start >=', $periodStart)
                              ->where('week_start <=', $periodEnd)
                              ->findAll();

        if (empty($timesheets)) {
            return null; // Nothing to process
        }

        // Group by user
        $userHours = [];
        $tsIdsToUpdate = [];
        foreach ($timesheets as $ts) {
            $uId = $ts['user_id'];
            if (!isset($userHours[$uId])) {
                $userHours[$uId] = 0;
            }
            $userHours[$uId] += $tsModel->totalHours($ts['id']);
            $tsIdsToUpdate[] = $ts['id'];
        }

        // Create the PayRun Record
        $runModel = new PayRunModel();
        
        $payRunId = $runModel->insert([
            'tenant_id'        => $tenantId,
            'branch_id'        => $branchId,
            'pay_period_start' => $periodStart,
            'pay_period_end'   => $periodEnd,
            'status'           => 'Draft',
            'approved_by'      => null
        ]);

        if (!$payRunId) {
            throw new \RuntimeException("Failed to create PayRun: " . json_encode($runModel->errors()));
        }

        $slipModel = new PaySlipModel();
        $db = \Config\Database::connect();
        
        foreach ($userHours as $userId => $hours) {
            $user = $db->table('fs_users')->where('id', $userId)->get()->getRow();
            if (!$user) continue;

            $taxRate = 0.15; // default 15%
            if (!empty($user->tax_profile_id)) {
                $taxProfile = (new TaxProfileModel())->find($user->tax_profile_id);
                if ($taxProfile) {
                    $taxRate = (float)$taxProfile['tax_rate'] / 100;
                }
            }

            // Standardize hourly rate assumption
            $hourlyRate = isset($user->hourly_rate) ? (float)$user->hourly_rate : 30.00;
            
            $grossPay = $hours * $hourlyRate;
            $taxes = $grossPay * $taxRate;
            $netPay = $grossPay - $taxes;

            $slipModel->insert([
                'pay_run_id'     => $payRunId,
                'user_id'        => $userId,
                'gross_pay'      => $grossPay,
                'net_pay'        => $netPay,
                'taxes_withheld' => $taxes,
                'deductions'     => 0.00
            ]);
        }

        // Mark timesheets as processed
        $db->table('timesheets')->whereIn('id', $tsIdsToUpdate)->update([
            'payroll_status' => 'Processed',
            'pay_run_id'     => $payRunId
        ]);

        return $payRunId;
    }
}
