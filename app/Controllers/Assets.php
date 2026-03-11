<?php

namespace App\Controllers;

use App\Models\AssetModel;
use App\Models\AssetAssignmentModel;
use App\Models\AssetMaintenanceModel;

class Assets extends BaseAppController
{
    /**
     * GET /assets
     * Renders the UI for managing branch assets.
     */
    public function index()
    {
        $data['title'] = 'Assets';
        $aModel = new AssetModel();
        // ErpModel automatically restricts by tenant_id/branch_id
        $data['assets'] = $aModel->findAll();
        
        return view('assets/index', $data);
    }

    /**
     * POST /assets
     * Registers a new asset under the active branch.
     */
    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tenantId = session('tenant_id');
        $branchId = session('branch_id');

        $data = [
            'tenant_id'                   => $tenantId,
            'branch_id'                   => $branchId,
            'asset_tag'                   => $this->request->getPost('asset_tag'),
            'name'                        => $this->request->getPost('name'),
            'category'                    => $this->request->getPost('category'),
            'status'                      => $this->request->getPost('status') ?: 'Active',
            'purchase_date'               => $this->request->getPost('purchase_date') ?: null,
            'purchase_price'              => $this->request->getPost('purchase_price') ?: null,
            'current_location_project_id' => $this->request->getPost('project_id') ?: null,
        ];

        (new AssetModel())->insert($data);
        return redirect()->back()->with('success', 'Equipment Registered.');
    }

    /**
     * POST /assets/:id/assign
     * Logs an asset assignment/transfer to a project or person.
     */
    public function assign(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $aModel = new AssetModel();
        $asset = $aModel->find($id); // Will inherit ABAC scope, protecting against cross-branch writes implicitly.
        if (!$asset) return redirect()->back()->with('error', 'Equipment not found.');

        $projectId = $this->request->getPost('project_id') ?: null;
        
        $assignData = [
            'asset_id'            => $id,
            'project_id'          => $projectId,
            'assigned_to_user_id' => $this->request->getPost('assigned_to_user_id') ?: null,
            'assigned_date'       => $this->request->getPost('assigned_date') ?: date('Y-m-d'),
            'status'              => 'Assigned'
        ];

        (new AssetAssignmentModel())->insert($assignData);

        // Update the asset's current location directly
        $aModel->update($id, [
            'current_location_project_id' => $projectId,
            'status'                      => 'In Use'
        ]);

        return redirect()->back()->with('success', 'Equipment Dispatched/Transferred.');
    }

    /**
     * POST /assets/:id/maintenance
     * Logs maintenance cost/history.
     */
    public function maintenance(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $aModel = new AssetModel();
        $asset = $aModel->find($id);
        if (!$asset) return redirect()->back()->with('error', 'Equipment not found.');

        $maintData = [
            'asset_id'         => $id,
            'maintenance_date' => $this->request->getPost('maintenance_date') ?: date('Y-m-d'),
            'description'      => $this->request->getPost('description'),
            'cost'             => $this->request->getPost('cost') ?: 0.00,
            'performed_by'     => $this->request->getPost('performed_by')
        ];

        (new AssetMaintenanceModel())->insert($maintData);
        return redirect()->back()->with('success', 'Maintenance Logged.');
    }
}
