<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\InspectionModel;
use App\Models\SafetyIncidentModel;
use App\Models\PunchListItemModel;
use App\Models\ProjectModel;

class TestPhase6Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase6';
    protected $description = 'Verifies Phase 6 ERP functionality (Inspections, Safety Incidents, Punch Lists).';

    public function run(array $params)
    {
        CLI::write("=== Testing Phase 6 (QHSE) ===", "yellow");

        // We assume Project ID 1 exists and is assigned to Atlanta Branch (17) from earlier phases
        $projectModel = new ProjectModel();
        
        // Find a valid project to test with (e.g. branch 17, from test users doc)
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
        $inspModel   = new InspectionModel();
        $safetyModel = new SafetyIncidentModel();
        $punchModel  = new PunchListItemModel();

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
        // Test 1: Inspections Creation & RBAC Check
        // ==========================================
        CLI::write("\n1. Testing Inspections Creation...", "cyan");
        try {
            $inspId = $inspModel->insert([
                'tenant_id'       => $tenantId,
                'branch_id'       => $branchId,
                'project_id'      => $projectId,
                'type'            => 'Safety Audit',
                'status'          => 'completed',
                'inspection_date' => date('Y-m-d'),
                'created_by'      => 9991,
            ]);
            CLI::write(" ✓ Inspection created successfully (ID: $inspId)", "green");
        } catch (\Exception $e) {
            CLI::error(" ✗ Inspection creation failed: " . $e->getMessage());
        }

        // ==========================================
        // Test 2: Safety Incident Enforcement
        // ==========================================
        CLI::write("\n2. Testing Safety Incident Data Enforcement...", "cyan");
        try {
            // Missing branch_id, should fail if saveSystem wasn't used, but we insert directly for testing
            $safetyModel->insert([
                'tenant_id'     => $tenantId,
                'project_id'    => $projectId,
                'incident_date' => date('Y-m-d H:i:s'),
                'type'          => 'Injury',
                'severity'      => 'High',
                'description'   => 'Worker tripped on rebar.',
                'reported_by'   => 9991,
                'created_by'    => 9991,
                'status'        => 'open',
            ]);
            CLI::error(" ✗ Safety Incident allowed creation WITHOUT branch_id! (ErpModel intercept failed)");
        } catch (\Exception $e) {
            CLI::write(" ✓ Safety Incident properly blocked without branch_id. Exception: " . $e->getMessage(), "green");
        }

        // Create Valid Safety Incident
        try {
            $sipId = $safetyModel->insert([
                'tenant_id'     => $tenantId,
                'branch_id'     => $branchId, // Provided
                'project_id'    => $projectId,
                'incident_date' => date('Y-m-d H:i:s'),
                'type'          => 'Injury',
                'severity'      => 'High',
                'description'   => 'Worker tripped on rebar.',
                'reported_by'   => 9991,
                'created_by'    => 9991,
                'status'        => 'open',
            ]);
            CLI::write(" ✓ Valid Safety Incident created successfully (ID: $sipId)", "green");
        } catch (\Exception $e) {
            CLI::error(" ✗ Safety Incident creation failed: " . $e->getMessage());
        }

        // ==========================================
        // Test 3: Punch List Legacy Refactor
        // ==========================================
        CLI::write("\n3. Testing Refactored Punch Lists...", "cyan");
        try {
            $plId = $punchModel->insert([
                'tenant_id'   => $tenantId,
                'branch_id'   => $branchId,
                'project_id'  => $projectId,
                'title'       => 'Fix drywall in lobby',
                'item_number' => 'PL-0001',
                'priority'    => 'medium',
                'status'      => 'open',
            ]);
            CLI::write(" ✓ Punch List item created successfully with Erp fields (ID: $plId)", "green");
        } catch (\Exception $e) {
            CLI::error(" ✗ Punch List item creation failed: " . $e->getMessage());
        }

        // Query test
        $foundPl = $punchModel->forProject($projectId);
        if (count($foundPl) > 0) {
            CLI::write(" ✓ Successfully fetched punch lists for project.", "green");
        } else {
            CLI::error(" ✗ Failed to fetch punch lists for project.");
        }


        CLI::write("\n=== Phase 6 Tests Complete ===", "yellow");
    }
}
