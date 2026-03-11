<?php

namespace App\Controllers;

use App\Models\InventoryItemModel;
use App\Models\InventoryLocationModel;
use App\Models\InventoryStockModel;
use App\Models\InventoryTransactionModel;

class Inventory extends BaseAppController
{
    /**
     * GET /inventory
     * Renders the UI Dashboard for warehouse inventory.
     */
    public function index()
    {
        $data['title'] = 'Inventory & Stock';
        
        $iModel = new InventoryItemModel();
        $lModel = new InventoryLocationModel();
        
        $data['items'] = $iModel->findAll();
        $data['locations'] = $lModel->findAll();

        $db = \Config\Database::connect();
        $data['stocks'] = $db->table('fs_inventory_stocks s')
            ->select('s.*, i.name as item_name, i.sku, i.unit_of_measure, l.name as location_name')
            ->join('fs_inventory_items i', 'i.id = s.item_id', 'left')
            ->join('fs_inventory_locations l', 'l.id = s.location_id', 'left')
            ->where('i.branch_id', session('branch_id'))
            ->get()->getResultArray();

        return view('inventory/index', $data);
    }

    /**
     * POST /inventory/items
     * Adds an item to the branch's catalog.
     */
    public function storeItem(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tenantId = session('tenant_id');
        $branchId = session('branch_id');

        $data = [
            'tenant_id'       => $tenantId,
            'branch_id'       => $branchId,
            'sku'             => $this->request->getPost('sku'),
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description'),
            'category'        => $this->request->getPost('category'),
            'unit_of_measure' => $this->request->getPost('unit_of_measure') ?: 'Each',
            'reorder_level'   => $this->request->getPost('reorder_level') ?: 0
        ];

        (new InventoryItemModel())->insert($data);
        return redirect()->back()->with('success', 'Material added to Inventory.');
    }

    /**
     * POST /inventory/locations
     * Defines a warehouse/storage location belonging to the branch.
     */
    public function storeLocation(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tenantId = session('tenant_id');
        $branchId = session('branch_id');

        $data = [
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'name'      => $this->request->getPost('name'),
            'address'   => $this->request->getPost('address')
        ];

        (new InventoryLocationModel())->insert($data);
        return redirect()->back()->with('success', 'Storage Yard/Job Site added.');
    }

    /**
     * POST /inventory/transactions
     * Logs an IN or OUT movement of stock and adjusts the running tally.
     */
    public function processTransaction(): \CodeIgniter\HTTP\RedirectResponse
    {
        $itemId    = (int) $this->request->getPost('item_id');
        $locationId= (int) $this->request->getPost('location_id');
        $qty       = (float) $this->request->getPost('quantity');
        $type      = $this->request->getPost('transaction_type'); // In/Out

        // 1. Log Transaction
        $record = [
            'item_id'                => $itemId,
            'location_id'            => $locationId,
            'project_id_destination' => $this->request->getPost('project_id') ?: null,
            'quantity'               => $qty,
            'transaction_type'       => $type,
            'date'                   => date('Y-m-d'),
            'user_id'                => $this->currentUser['id']
        ];
        (new InventoryTransactionModel())->insert($record);

        // 2. Adjust Stock Tally
        $sModel = new InventoryStockModel();
        $stock = $sModel->where('item_id', $itemId)->where('location_id', $locationId)->first();
        
        $multiplier = ($type === 'In') ? 1 : -1;
        $adjustment = $qty * $multiplier;

        if ($stock) {
            $sModel->update($stock['id'], [
                'quantity'   => (float)$stock['quantity'] + $adjustment,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $sModel->insert([
                'item_id'     => $itemId,
                'location_id' => $locationId,
                'quantity'    => $adjustment,
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back()->with('success', 'Material transfer processed.');
    }
}
