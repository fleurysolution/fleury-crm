<?php

namespace App\Controllers;

use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\ProjectModel;

class Estimates extends BaseAppController
{
    /**
     * POST /projects/:id/estimates
     * Creates a new empty master estimate.
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $eModel = new EstimateModel();
        
        $estimateId = $eModel->insert([
            'project_id'   => $projectId,
            'title'        => $this->request->getPost('title'),
            'status'       => 'Draft',
            'total_amount' => 0.00,
            'created_by'   => $this->currentUser['id'],
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to(site_url("estimates/{$estimateId}"))->with('success', 'Estimate created. Now add your items.');
    }

    /**
     * GET /estimates/:id
     * Detail view to build line items.
     */
    public function show(int $id): string
    {
        $eModel = new EstimateModel();
        $estimate = $eModel->find($id);
        if (!$estimate) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $project = (new ProjectModel())->find($estimate['project_id']);
        $items = (new EstimateItemModel())->forEstimate($id);

        return $this->render('estimates/show', [
            'project'  => $project,
            'estimate' => $estimate,
            'items'    => $items,
        ]);
    }

    /**
     * POST /estimates/:id/items
     * Add a line item to the estimate.
     */
    public function addItem(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $eModel = new EstimateModel();
        $estimate = $eModel->find($id);
        if (!$estimate) return $this->response->setJSON(['success' => false, 'message' => 'Estimate not found.']);

        $qty = (float)$this->request->getPost('quantity');
        $cost = (float)$this->request->getPost('unit_cost');
        $total = $qty * $cost;

        (new EstimateItemModel())->insert([
            'estimate_id' => $id,
            'cost_code'   => $this->request->getPost('cost_code'),
            'description' => $this->request->getPost('description'),
            'quantity'    => $qty,
            'unit'        => $this->request->getPost('unit'),
            'unit_cost'   => $cost,
            'total_cost'  => $total
        ]);

        // Update Master Total
        $newAmount = $estimate['total_amount'] + $total;
        $eModel->update($id, ['total_amount' => $newAmount]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->back()->with('success', 'Item Added.');
    }

    /**
     * POST /estimates/:id/items/:itemId/delete
     */
    public function deleteItem(int $id, int $itemId): \CodeIgniter\HTTP\RedirectResponse
    {
        $eModel = new EstimateModel();
        $iModel = new EstimateItemModel();

        $estimate = $eModel->find($id);
        $item = $iModel->find($itemId);

        if ($estimate && $item && $item['estimate_id'] == $id) {
            $iModel->delete($itemId);
            // Deduct from Master Total
            $newAmount = max(0, $estimate['total_amount'] - $item['total_cost']);
            $eModel->update($id, ['total_amount' => $newAmount]);
            
            return redirect()->back()->with('success', 'Item Deleted.');
        }
        return redirect()->back()->with('error', 'Item not found.');
    }

    /**
     * POST /estimates/:id/delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $eModel = new EstimateModel();
        $est    = $eModel->find($id);
        if ($est) {
            $eModel->delete($id);
            return redirect()->to(site_url("projects/{$est['project_id']}?tab=estimates"))
                ->with('success', 'Estimate deleted.');
        }
        return redirect()->back()->with('error', 'Estimate not found.');
    }
}
