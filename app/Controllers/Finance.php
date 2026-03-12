<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\SovItemModel;
use App\Models\PayAppModel;
use App\Models\PayAppItemModel;
use App\Models\ProjectExpenseModel;

class Finance extends BaseAppController
{
    /**
     * POST /projects/:id/sov
     * Add a Schedule of Values line item
     */
    public function storeSovItem(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $sModel = new SovItemModel();
        
        $sModel->insert([
            'project_id'      => $projectId,
            'item_no'         => $this->request->getPost('item_no'),
            'description'     => $this->request->getPost('description'),
            'scheduled_value' => (float)$this->request->getPost('scheduled_value'),
            'created_by'      => $this->currentUser['id']
        ]);

        return redirect()->back()->with('success', 'SOV Item Added.');
    }

    /**
     * POST /projects/:id/sov/:id/delete
     */
    public function deleteSovItem(int $projectId, int $itemId): \CodeIgniter\HTTP\RedirectResponse
    {
        $sModel = new SovItemModel();
        $item = $sModel->find($itemId);
        if ($item && $item['project_id'] == $projectId) {
            $sModel->delete($itemId);
            return redirect()->back()->with('success', 'SOV Item deleted.');
        }
        return redirect()->back()->with('error', 'SOV Item not found.');
    }

    /**
     * POST /projects/:id/pay-apps
     * Generate a new master Payment Application
     */
    public function createPayApp(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $pModel = new PayAppModel();
        
        // Find next App number
        $existing = $pModel->where('project_id', $projectId)->findAll();
        $nextAppNo = count($existing) + 1;

        $project = (new ProjectModel())->find($projectId);
        if (!$project) {
            return redirect()->back()->with('error', 'Project not found.');
        }

        $payAppId = $pModel->insert([
            'project_id'           => $projectId,
            'tenant_id'            => $project['tenant_id'],
            'branch_id'            => $project['branch_id'],
            'application_no'       => $nextAppNo,
            'period_to'            => $this->request->getPost('period_to'),
            'status'               => 'Draft',
            'retainage_percentage' => (float)($this->request->getPost('retainage_percentage') ?? 10.0),
            'created_by'           => $this->currentUser['id']
        ]);

        return redirect()->to(site_url("finance/pay-apps/{$payAppId}"))->with('success', "Payment Application #{$nextAppNo} Draft Created. Please fill out line items.");
    }

    /**
     * GET /finance/pay-apps/:id
     * The Worksheet for a specific Pay App
     */
    public function showPayApp(int $id): string
    {
        $pModel  = new PayAppModel();
        $piModel = new PayAppItemModel();
        $sModel  = new SovItemModel();
        
        $payApp = $pModel->find($id);
        if (!$payApp) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $project = (new ProjectModel())->find($payApp['project_id']);
        
        // Get Master SOV lines
        $sovLines = $sModel->forProject($payApp['project_id']);

        // Get Approved Change Orders
        $coModel = new \App\Models\ChangeOrderModel();
        $changeOrders = $coModel->where(['project_id' => $payApp['project_id'], 'status' => 'approved'])->findAll();
        
        // Get already saved progress for THIS app (if any)
        $savedItems = $piModel->where('pay_app_id', $id)->findAll();
        $mappedItems = [];
        foreach ($savedItems as $item) {
            if ($item['sov_item_id']) {
                $mappedItems[$item['sov_item_id']] = $item;
            } elseif ($item['change_order_id']) {
                $mappedItems['co_' . $item['change_order_id']] = $item;
            }
        }

        // Get Previous Applications (to calculate Previous Work Completed)
        $previousApps = $pModel->where('project_id', $payApp['project_id'])
                               ->where('application_no <', $payApp['application_no'])
                               ->findAll();
        $prevAppIds = array_column($previousApps, 'id');
        
        $previousProgress = [];
        if (!empty($prevAppIds)) {
            $prevItems = $piModel->whereIn('pay_app_id', $prevAppIds)->findAll();
            foreach ($prevItems as $pi) {
                $sid = $pi['sov_item_id'];
                if (!isset($previousProgress[$sid])) $previousProgress[$sid] = 0;
                $previousProgress[$sid] += $pi['work_completed_this_period'] + $pi['materials_presently_stored'];
            }
        }

        return $this->render('finance/pay_app_show', [
            'project'          => $project,
            'payApp'           => $payApp,
            'sovLines'         => $sovLines,
            'changeOrders'     => $changeOrders,
            'mappedItems'      => $mappedItems,
            'previousProgress' => $previousProgress
        ]);
    }

    /**
     * POST /finance/pay-apps/:id/items
     * Save the work completed progress matrix.
     */
    public function savePayAppItems(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $pModel  = new PayAppModel();
        $piModel = new PayAppItemModel();
        
        $payApp = $pModel->find($id);
        if (!$payApp) return redirect()->back()->with('error', 'Pay App not found.');

        // Wipe existing items for this app and re-insert the grid
        $piModel->where('pay_app_id', $id)->delete();

        $workPerLine = $this->request->getPost('work_completed') ?? [];
        $matPerLine  = $this->request->getPost('materials_stored') ?? [];
        
        $coWorkPerLine = $this->request->getPost('co_work_completed') ?? [];
        $coMatPerLine  = $this->request->getPost('co_materials_stored') ?? [];

        $insertData = [];
        foreach ($workPerLine as $sovItemId => $amountComp) {
            $mat = isset($matPerLine[$sovItemId]) ? (float)$matPerLine[$sovItemId] : 0.00;
            $insertData[] = [
                'pay_app_id'                 => $id,
                'sov_item_id'                => $sovItemId,
                'change_order_id'            => null,
                'work_completed_this_period' => (float)$amountComp,
                'materials_presently_stored' => $mat
            ];
        }

        foreach ($coWorkPerLine as $coId => $amountComp) {
            $mat = isset($coMatPerLine[$coId]) ? (float)$coMatPerLine[$coId] : 0.00;
            $insertData[] = [
                'pay_app_id'                 => $id,
                'sov_item_id'                => null,
                'change_order_id'            => $coId,
                'work_completed_this_period' => (float)$amountComp,
                'materials_presently_stored' => $mat
            ];
        }
        
        if (!empty($insertData)) {
            $piModel->insertBatch($insertData);
        }

        // Check if updating status
        $statusAction = $this->request->getPost('status_action');
        if ($statusAction === 'submit') {
            $pModel->update($id, ['status' => 'Submitted']);
            
            // Trigger Workflow
            $workflow = new \App\Services\WorkflowEngine();
            // Calculate total for amount-based routing? PayAppItems total
            $totalVal = 0;
            $items = $piModel->where('pay_app_id', $id)->findAll();
            // Logic to calculate total amount from items (work_completed * planned_value etc)
            // For now, use a sum of work_completed_this_period
            foreach($items as $i) $totalVal += $i['work_completed_this_period'];

            $reqId = $workflow->submitRequest('pay_apps', 'pay_app', $id, $this->currentUser['id'], [], session('branch_id'), (float)$totalVal);
            
            if (!$reqId) {
                $pModel->update($id, ['status' => 'Approved']);
                return redirect()->to(site_url("projects/{$payApp['project_id']}?tab=finance"))->with('success', 'Payment Application automatically approved.');
            }

            return redirect()->to(site_url("projects/{$payApp['project_id']}?tab=finance"))->with('success', 'Payment Application Submitted for approval.');
        }

        return redirect()->back()->with('success', 'Progress saved successfully.');
    }

    /**
     * GET /finance/pay-apps/:id/pdf
     * Export the Pay App as a printable PDF
     */
    public function exportPayAppPdf(int $id)
    {
        $pModel  = new PayAppModel();
        $piModel = new PayAppItemModel();
        $sModel  = new SovItemModel();
        
        $payApp = $pModel->find($id);
        if (!$payApp) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $project = (new ProjectModel())->find($payApp['project_id']);
        $sovLines = $sModel->forProject($payApp['project_id']);

        // Get Approved Change Orders
        $coModel = new \App\Models\ChangeOrderModel();
        $changeOrders = $coModel->where(['project_id' => $payApp['project_id'], 'status' => 'approved'])->findAll();
        
        $savedItems = $piModel->where('pay_app_id', $id)->findAll();
        $mappedItems = [];
        foreach ($savedItems as $item) {
            if ($item['sov_item_id']) {
                $mappedItems[$item['sov_item_id']] = $item;
            } elseif ($item['change_order_id']) {
                $mappedItems['co_' . $item['change_order_id']] = $item;
            }
        }

        $previousApps = $pModel->where('project_id', $payApp['project_id'])
                               ->where('application_no <', $payApp['application_no'])
                               ->findAll();
        $prevAppIds = array_column($previousApps, 'id');
        
        $previousProgress = [];
        if (!empty($prevAppIds)) {
            $prevItems = $piModel->whereIn('pay_app_id', $prevAppIds)->findAll();
            foreach ($prevItems as $pi) {
                if ($pi['sov_item_id']) {
                    $sid = $pi['sov_item_id'];
                    if (!isset($previousProgress[$sid])) $previousProgress[$sid] = 0;
                    $previousProgress[$sid] += $pi['work_completed_this_period'] + $pi['materials_presently_stored'];
                } elseif ($pi['change_order_id']) {
                    $key = 'co_' . $pi['change_order_id'];
                    if (!isset($previousProgress[$key])) $previousProgress[$key] = 0;
                    $previousProgress[$key] += $pi['work_completed_this_period'] + $pi['materials_presently_stored'];
                }
            }
        }

        $html = view('finance/pay_app_pdf', [
            'project'          => $project,
            'payApp'           => $payApp,
            'sovLines'         => $sovLines,
            'changeOrders'     => $changeOrders,
            'mappedItems'      => $mappedItems,
            'previousProgress' => $previousProgress
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = "PayApp_" . str_pad($payApp['application_no'], 3, '0', STR_PAD_LEFT) . "_" . date('Ymd', strtotime($payApp['period_to'])) . '.pdf';
        
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }

    /**
     * POST /finance/expenses/:projectId
     */
    public function storeExpense(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $eModel = new ProjectExpenseModel();
        
        $data = [
            'project_id'  => $projectId,
            'tenant_id'   => session('tenant_id'),
            'branch_id'   => session('branch_id'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'amount'      => (float)$this->request->getPost('amount'),
            'currency'    => $this->request->getPost('currency') ?? 'USD',
            'expense_date'=> $this->request->getPost('expense_date') ?? date('Y-m-d'),
            'vendor'      => $this->request->getPost('vendor'),
            'status'      => 'submitted',
            'submitted_by'=> $this->currentUser['id']
        ];

        // Handle File Upload for receipt
        $file = $this->request->getFile('receipt');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/expenses', $newName);
            $data['receipt_path'] = 'uploads/expenses/' . $newName;
        }

        $expenseId = $eModel->insert($data);

        // Trigger Workflow
        $workflow = new \App\Services\WorkflowEngine();
        $reqId = $workflow->submitRequest('expenses', 'project_expense', $expenseId, $this->currentUser['id'], [], session('branch_id'), $data['amount']);
        
        if (!$reqId) {
            $eModel->update($expenseId, ['status' => 'approved', 'approved_at' => date('Y-m-d H:i:s')]);
            return redirect()->back()->with('success', 'Expense created and automatically approved.');
        }

        return redirect()->back()->with('success', 'Expense submitted for approval.');
    }

    /**
     * POST /finance/expenses/:id/delete
     */
    public function deleteExpense(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $eModel = new ProjectExpenseModel();
        $expense = $eModel->find($id);
        if ($expense) {
            $eModel->delete($id);
            return redirect()->back()->with('success', 'Expense deleted.');
        }
        return redirect()->back()->with('error', 'Expense not found.');
    }
}
