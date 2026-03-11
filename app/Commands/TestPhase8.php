<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AssetModel;
use App\Models\InventoryItemModel;
use App\Models\InventoryLocationModel;
use App\Models\InventoryStockModel;

class TestPhase8 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase8';
    protected $description = 'Verifies Phase 8 Inventory & Asset data linkage';

    public function run(array $params)
    {
        CLI::write("Testing Phase 8: Inventory & Asset ABAC Scope", 'green');
        
        $assetModel = new AssetModel();
        $itemModel  = new InventoryItemModel();
        $locModel   = new InventoryLocationModel();
        $stockModel = new InventoryStockModel();

        // 1. Setup Session Context for Branch 10
        $_SESSION['is_logged_in'] = true;
        $_SESSION['tenant_id'] = 1; 
        $_SESSION['branch_id'] = 10;
        $_SESSION['user_id'] = 1010;
        $_SESSION['geo_access_permission'] = 'branch';
        $_SESSION['user_roles'] = ['warehouse_manager'];
        
        CLI::write("1. Simulating Inventory/Asset creation by Branch 10 Warehouse Manager...", 'yellow');
        
        $assetId = $assetModel->insert([
            'tenant_id' => 1,
            'branch_id' => 10,
            'asset_tag' => 'CAT-320',
            'name'      => 'Excavator CAT 320',
            'status'    => 'Active'
        ]);

        $itemId = $itemModel->insert([
            'tenant_id' => 1,
            'branch_id' => 10,
            'sku'       => 'CEM-42.5',
            'name'      => 'Cement 42.5 OPC',
            'unit_of_measure' => 'Bag'
        ]);

        $locId = $locModel->insert([
            'tenant_id' => 1,
            'branch_id' => 10,
            'name'      => 'Main Warehouse B10'
        ]);

        // Assertions for Branch 10
        $aCount = $assetModel->countAllResults();
        $iCount = $itemModel->countAllResults();
        $lCount = $locModel->countAllResults();
        
        if ($aCount >= 1 && $iCount >= 1 && $lCount >= 1) {
            CLI::write("  [PASS] Assets and Inventory records retrieved under Branch 10 scope.", 'green');
        } else {
            CLI::write("  [FAIL] Data retrieval failed for Branch 10.", 'red');
            return;
        }

        // 2. Test Cross-Branch Scope Block (Branch 20)
        CLI::write("\n2. Testing Cross-Branch Scope Block (Branch 20)...", 'yellow');
        $_SESSION['branch_id'] = 20; 
        
        $aCross = $assetModel->find($assetId);
        $iCross = $itemModel->find($itemId);
        $lCross = $locModel->find($locId);
        
        if (!$aCross && !$iCross && !$lCross) {
            CLI::write("  [PASS] Branch 20 User was blocked from seeing Branch 10 private Assets/Inventory.", 'green');
        } else {
            CLI::write("  [FAIL] ABAC Leak: Branch 20 user accessed Branch 10 data.", 'red');
        }

        // Cleanup
        $_SESSION['user_roles'] = ['admin']; // Admin clears scope
        $assetModel->delete($assetId, true);
        $itemModel->delete($itemId, true);
        $locModel->delete($locId, true);
        
        CLI::write("\nPhase 8 tests completed.", 'green');
    }
}
