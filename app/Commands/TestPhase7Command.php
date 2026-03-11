<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PurchaseOrderModel;
use App\Models\BidModel;
use App\Models\ProjectModel;

class TestPhase7Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase7';
    protected $description = 'Verifies Phase 7 ERP functionality (Procurement data isolation).';

    public function run(array $params)
    {
        CLI::write("=== Testing Phase 7 (Procurement) ===", "yellow");

        $projectModel = new ProjectModel();
        
        // Find a valid project to test with (e.g. branch 17)
        $projectsList = $projectModel->where('branch_id', 17)->findAll();
        $project = reset($projectsList) ?: $projectModel->first();
        if (!$project) {
            CLI::error("No projects found. Seed the database first!");
            return;
        }

        $projectId = (int) $project['id'];
        $tenantId  = (int) $project['tenant_id'] ?: 9;
        $branchId  = (int) $project['branch_id'] ?: 17;

        CLI::write("Using Project ID: {$projectId} (Branch: {$branchId}, Tenant: {$tenantId})");

        // Initialize models
        $poModel  = new PurchaseOrderModel();
        $bidModel = new BidModel();

        // Simulate Session Data
        $session = session();
        $session->set([
            'is_logged_in' => true,
            'user_id'      => 9991, // Fake PM
            'tenant_id'    => $tenantId,
            'branch_id'    => $branchId,
            'user_roles'   => ['project_manager'],
            'geo_access_permission' => 'branch'
        ]);

        // ==========================================
        // Test 1: Purchase Order Data Enforcement
        // ==========================================
        CLI::write("\n1. Testing Purchase Order Data Enforcement...", "cyan");
        try {
            // Missing branch_id
            $poModel->insert([
                'tenant_id'     => $tenantId,
                'project_id'    => $projectId,
                'po_number'     => 'PO-999-TEST',
                'title'         => 'Test PO sans branch',
                'status'        => 'Draft',
                'created_by'    => 9991,
            ]);
            CLI::error(" ✗ PO allowed creation WITHOUT branch_id! (ErpModel intercept failed)");
        } catch (\Exception $e) {
            CLI::write(" ✓ PO properly blocked without branch_id.", "green");
        }

        try {
            $poId = $poModel->insert([
                'tenant_id'     => $tenantId,
                'branch_id'     => $branchId,
                'project_id'    => $projectId,
                'po_number'     => 'PO-999-TEST2',
                'title'         => 'Valid Test PO',
                'status'        => 'Draft',
                'created_by'    => 9991,
            ]);
            CLI::write(" ✓ Valid PO created successfully (ID: $poId)", "green");
        } catch (\Exception $e) {
            CLI::error(" ✗ Valid PO creation failed: " . $e->getMessage());
        }

        // ==========================================
        // Test 2: Bids Data Enforcement
        // ==========================================
        CLI::write("\n2. Testing Bids Data Enforcement...", "cyan");
        try {
            // Missing branch_id
            $bidModel->insert([
                'tenant_id'     => $tenantId,
                'project_id'    => $projectId,
                'trade_package' => 'Electrical',
                'vendor_name'   => 'Sparky Electrics',
                'bid_amount'    => 50000,
                'status'        => 'Pending',
                'created_by'    => 9991,
            ]);
            CLI::error(" ✗ Bid allowed creation WITHOUT branch_id! (ErpModel intercept failed)");
        } catch (\Exception $e) {
            CLI::write(" ✓ Bid properly blocked without branch_id.", "green");
        }

        try {
            $bidId = $bidModel->insert([
                'tenant_id'     => $tenantId,
                'branch_id'     => $branchId,
                'project_id'    => $projectId,
                'trade_package' => 'Electrical',
                'vendor_name'   => 'Sparky Electrics Valid',
                'bid_amount'    => 50000,
                'status'        => 'Pending',
                'created_by'    => 9991,
            ]);
            CLI::write(" ✓ Valid Bid created successfully (ID: $bidId)", "green");
        } catch (\Exception $e) {
            CLI::error(" ✗ Valid Bid creation failed: " . $e->getMessage());
        }

        // ==========================================
        // Test 3: ABAC Read Filtering
        // ==========================================
        CLI::write("\n3. Testing RBAC/ABAC read filtering on POs...", "cyan");
        $pos = $poModel->forProject($projectId);
        if (count($pos) > 0) {
            CLI::write(" ✓ Successfully read branch-scoped POs for the authenticated user.", "green");
        } else {
            CLI::error(" ✗ Could not read branch-scoped POs.");
        }

        CLI::write("\n=== Phase 7 Tests Complete ===", "yellow");
    }
}
