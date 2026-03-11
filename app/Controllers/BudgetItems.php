<?php

namespace App\Controllers;

use App\Models\ProjectBudgetItemModel;

class BudgetItems extends BaseAppController
{
    protected $budgetItems;

    public function __construct()
    {
        $this->budgetItems = new ProjectBudgetItemModel();
    }

    public function store(int $projectId)
    {
        $tenantId = session()->get('tenant_id');
        
        $qty = (float)$this->request->getPost('quantity');
        $unitCost = (float)$this->request->getPost('unit_cost');
        
        $data = [
            'project_id'   => $projectId,
            'tenant_id'    => $tenantId,
            'cost_code_id' => $this->request->getPost('cost_code_id') ?: null,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'quantity'     => $qty,
            'unit'         => $this->request->getPost('unit') ?: 'LS',
            'unit_cost'    => $unitCost,
            'total_cost'   => $qty * $unitCost,
        ];

        $this->budgetItems->insert($data);

        return redirect()->to(site_url("projects/{$projectId}?tab=finance"))
            ->with('success', 'Budget item added.');
    }

    public function delete(int $id)
    {
        $item = $this->budgetItems->find($id);
        if (!$item) return redirect()->back()->with('error', 'Item not found.');
        
        $this->budgetItems->delete($id);
        
        return redirect()->to(site_url("projects/{$item['project_id']}?tab=finance"))
            ->with('success', 'Budget item removed.');
    }
}
