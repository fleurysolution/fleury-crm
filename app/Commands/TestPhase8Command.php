<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AssetModel;
use App\Models\AssetAssignmentModel;
use App\Models\AssetMaintenanceModel;
use App\Models\InventoryItemModel;
use App\Models\InventoryLocationModel;
use App\Models\InventoryStockModel;
use App\Models\InventoryTransactionModel;
use App\Models\ProjectModel;

class TestPhase8Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase8';
    protected $description = 'Verifies ABAC enforcement for Assets & Inventory components.';

    public function run(array $params)
    {
        CLI::write("\n=== Testing Phase 8 (Assets & Inventory) ===", 'yellow');

        // Setup a mock auth session
        $mockUserId = 1;
        $mockBranchId = 17;
        $mockTenantId = 9;

        $_SESSION['user_id']   = $mockUserId;
        $_SESSION['branch_id'] = $mockBranchId;
        $_SESSION['tenant_id'] = $mockTenantId;
        $_SESSION['role_id']   = 2; // Project Manager or similar
        $_SESSION['user_type'] = 'staff';

        // Find a project to link to
        $projModel = new ProjectModel();
        $project = $projModel->first();
        if (!$project) {
            CLI::error("No active project found. Seed data first.");
            return;
        }

        $mockBranchId = (int) $project['branch_id'] ?: 17;
        $mockTenantId = (int) $project['tenant_id'] ?: 9;

        $_SESSION['branch_id'] = $mockBranchId;
        $_SESSION['tenant_id'] = $mockTenantId;

        CLI::write("Using Project ID: {$project['id']} (Branch: $mockBranchId, Tenant: $mockTenantId)\n", 'cyan');

        // ---------------------------------------------------------------------
        // 1. ASSET MANAGEMENT
        // ---------------------------------------------------------------------
        CLI::write("1. Testing Asset Data Enforcement...", 'white');

        // Test missing branch_id scenario
        $_SESSION['branch_id'] = null;
        $assetModel = new AssetModel();

        try {
            $assetModel->insert([
                'tenant_id' => $mockTenantId,
                'asset_tag' => 'EQ-0001',
                'name' => 'Excavator Model X'
            ]);
            CLI::error(" ✗ ErpModel failed to catch empty branch_id on Asset creation.");
        } catch (\Exception $e) {
            CLI::write(" ✓ Asset properly blocked without branch_id.", 'green');
        }

        // Test valid creation
        $_SESSION['branch_id'] = $mockBranchId;
        
        $assetData = [
            'tenant_id' => $mockTenantId,
            'branch_id' => $mockBranchId,
            'asset_tag' => 'EQ-0001-' . time(), // unique
            'name'      => 'Test Excavator',
            'category'  => 'Machinery'
        ];

        try {
            $assetId = $assetModel->insert($assetData);
            if ($assetId) {
                CLI::write(" ✓ Valid Asset created successfully (ID: $assetId)", 'green');

                // Test Assignment
                $assignId = (new AssetAssignmentModel())->insert([
                    'asset_id'      => $assetId,
                    'project_id'    => $project['id'],
                    'assigned_date' => date('Y-m-d'),
                    'status'        => 'Assigned'
                ]);
                $assetModel->update($assetId, ['current_location_project_id' => $project['id']]);
                
                if ($assignId) {
                    CLI::write(" ✓ Valid Asset Assignment logged successfully.", 'green');
                } else {
                    CLI::error(" ✗ Asset assignment creation failed.");
                }

                // Test Maintenance
                $maintId = (new AssetMaintenanceModel())->insert([
                    'asset_id'         => $assetId,
                    'maintenance_date' => date('Y-m-d'),
                    'description'      => 'Oil Change',
                    'cost'             => 250.00
                ]);
                if ($maintId) {
                    CLI::write(" ✓ Valid Asset Maintenance logged successfully.", 'green');
                } else {
                    CLI::error(" ✗ Asset maintenance creation failed.");
                }

            } else {
                CLI::error(" ✗ Valid Asset creation failed: " . json_encode($assetModel->errors()));
            }
        } catch (\Exception $e) {
            CLI::error(" ✗ Valid Asset creation threw exception: " . $e->getMessage());
        }


        // ---------------------------------------------------------------------
        // 2. INVENTORY MANAGEMENT
        // ---------------------------------------------------------------------
        CLI::write("\n2. Testing Inventory Data Enforcement...", 'white');
        
        $itemModel = new InventoryItemModel();
        
        $itemData = [
            'tenant_id' => $mockTenantId,
            'branch_id' => $mockBranchId,
            'sku'       => 'CEM-001-' . time(),
            'name'      => 'Portland Cement Bag 50lb',
            'category'  => 'Materials'
        ];

        try {
            $itemId = $itemModel->insert($itemData);
            if ($itemId) {
                CLI::write(" ✓ Valid Inventory Item created successfully (ID: $itemId)", 'green');

                // Test Location Creation
                $locModel = new InventoryLocationModel();
                $locId = $locModel->insert([
                    'tenant_id' => $mockTenantId,
                    'branch_id' => $mockBranchId,
                    'name'      => 'Main Warehouse A'
                ]);

                if ($locId) {
                    CLI::write(" ✓ Valid Inventory Location created successfully.", 'green');

                    // Process Transaction In
                    $transModel = new InventoryTransactionModel();
                    $transId = $transModel->insert([
                        'item_id'          => $itemId,
                        'location_id'      => $locId,
                        'quantity'         => 100,
                        'transaction_type' => 'In',
                        'date'             => date('Y-m-d'),
                        'user_id'          => $mockUserId
                    ]);

                    if ($transId) {
                        CLI::write(" ✓ Valid Inventory Transaction (IN) logged successfully.", 'green');

                        // Update Stock
                        $stockModel = new InventoryStockModel();
                        $stockId = $stockModel->insert([
                            'item_id'     => $itemId,
                            'location_id' => $locId,
                            'quantity'    => 100,
                            'updated_at'  => date('Y-m-d H:i:s')
                        ]);

                        if ($stockId) {
                            CLI::write(" ✓ Inventory Stock correctly initialized.", 'green');
                        } else {
                           CLI::error(" ✗ Stock initialization failed.");
                        }

                    } else {
                        CLI::error(" ✗ Transaction creation failed.");
                    }

                } else {
                    CLI::error(" ✗ Location creation failed.");
                }

            } else {
                CLI::error(" ✗ Valid Inventory Item creation failed: " . json_encode($itemModel->errors()));
            }
        } catch (\Exception $e) {
             CLI::error(" ✗ Valid Inventory Item creation threw exception: " . $e->getMessage());
        }

        // ---------------------------------------------------------------------
        // 3. READ SCOPING VERIFICATION
        // ---------------------------------------------------------------------
        CLI::write("\n3. Testing RBAC/ABAC read filtering on Assets...", 'white');
        
        try {
            $myAssets = $assetModel->findAll();
            $foreignAssetCount = 0;
            foreach ($myAssets as $asset) {
                if ($asset['branch_id'] != $mockBranchId) {
                    $foreignAssetCount++;
                }
            }
            if ($foreignAssetCount > 0) {
                 CLI::error(" ✗ Asset read filtering failed. Found $foreignAssetCount records belonging to other branches!");
            } else {
                 CLI::write(" ✓ Successfully read branch-scoped Assets for the authenticated user.", 'green');
            }
        } catch (\Exception $e) {
            CLI::error(" ✗ Asset read threw exception: " . $e->getMessage());
        }

        CLI::write("\n=== Phase 8 Tests Complete ===\n", 'yellow');
    }
}
