<div class="row pt-2">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0">WIP Report (Work In Progress)</h6>
                <div class="small text-muted">Real-time Over/Under Billing Analysis</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Metric</th>
                                <th class="text-end">Value</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $contractVal = $project['budget'] ?? 0;
                                $costToDate  = $budget_data['actual_costs'] ?? 0;
                                $estimatedTotalCost = $contractVal * 0.85; // Simple assumption for demo
                                $percentComplete = ($estimatedTotalCost > 0) ? min(100, ($costToDate / $estimatedTotalCost) * 100) : 0;
                                $earnedRevenue = ($contractVal * $percentComplete) / 100;
                                $billedToDate = $budget_data['billed_to_date'] ?? 0;
                                $overUnder = $earnedRevenue - $billedToDate;
                            ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">Contract Sum</td>
                                <td class="text-end fw-bold"><?= number_format($contractVal, 2) ?></td>
                                <td class="text-muted small">Total contract value including changes</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-dark">Costs to Date</td>
                                <td class="text-end"><?= number_format($costToDate, 2) ?></td>
                                <td class="text-muted small">Aggregate of all recorded expenses</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-dark">% Complete (Cost-to-Cost)</td>
                                <td class="text-end text-primary fw-bold"><?= number_format($percentComplete, 1) ?>%</td>
                                <td class="text-muted small">Based on actual vs. estimated total cost</td>
                            </tr>
                            <tr class="table-light">
                                <td class="ps-4 fw-bold text-dark">Earned Revenue</td>
                                <td class="text-end fw-bold text-success"><?= number_format($earnedRevenue, 2) ?></td>
                                <td class="text-muted small">Revenue that SHOULD have been billed</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-dark">Total Billed</td>
                                <td class="text-end"><?= number_format($billedToDate, 2) ?></td>
                                <td class="text-muted small">Total amount of approved invoices</td>
                            </tr>
                            <tr class="bg-<?= $overUnder >= 0 ? 'success' : 'danger' ?> bg-opacity-10">
                                <td class="ps-4 fw-bold text-<?= $overUnder >= 0 ? 'success' : 'danger' ?>">
                                    <?= $overUnder >= 0 ? 'Under-Billed (Asset)' : 'Over-Billed (Liability)' ?>
                                </td>
                                <td class="text-end fw-bold text-<?= $overUnder >= 0 ? 'success' : 'danger' ?>">
                                    <?= number_format(abs($overUnder), 2) ?>
                                </td>
                                <td class="text-<?= $overUnder >= 0 ? 'success' : 'danger' ?> small">
                                    <?= $overUnder >= 0 ? 'Revenue earned but not yet invoiced' : 'Invoiced in excess of revenue earned' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
