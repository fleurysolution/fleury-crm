<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestPhase5Command extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:phase5';
    protected $description = 'Verifies Phase 5 ERP functionality (RFIs, Submittals, Drawings)';

    public function run(array $params)
    {
        CLI::write("--- Testing Phase 5: Field Collaboration ---", 'yellow');

        // Simulate a logged-in user at branch 10 and tenant 1
        $session = session();
        $session->set([
            'user_id' => 1,
            'tenant_id' => 1,
            'branch_id' => 10,
            'is_logged_in' => true
        ]);

        $projects = new \App\Models\ProjectModel();
        $rfis = new \App\Models\RfiModel();
        $submittals = new \App\Models\SubmittalModel();
        $drawings = new \App\Models\DrawingModel();

        // 1. Create a Project
        try {
            $projectId = $projects->insert([
                'title' => 'Test ERP Project - Branch 10 (Phase 5)',
                'tenant_id' => 1,
                'branch_id' => 10,
                'contract_type' => 'lump_sum',
                'versioned_budget_baseline' => 50000.00,
                'project_stage' => 'active'
            ]);
            CLI::write("Successfully created Project ID {$projectId}", 'green');
        } catch (\Exception $e) {
            CLI::error("Error inserting project: " . $e->getMessage());
            return;
        }

        // 2. Create an RFI
        try {
            $rfiId = $rfis->insert([
                'project_id' => $projectId,
                'tenant_id' => 1,
                'branch_id' => 10,
                'rfi_number' => 'RFI-001',
                'title' => 'Wall color mismatch',
                'question' => 'The paint color in room 101 does not match the spec. Please advise.',
                'created_by' => 1
            ]);
            
            $r = $rfis->find($rfiId);
            CLI::write("Successfully created RFI ID {$rfiId}", 'green');
            CLI::write("  -> Linked Branch ID: " . ($r['branch_id'] ?? 'NULL'));

            if ($r['branch_id'] != 10) {
                CLI::error("FAILED: Expected branch_id to be 10 on RFI.");
                return;
            }
        } catch (\Exception $e) {
            CLI::error("Error inserting RFI: " . $e->getMessage());
            return;
        }

        // 3. Create a Submittal
        try {
            $subId = $submittals->insert([
                'project_id' => $projectId,
                'tenant_id' => 1,
                'branch_id' => 10,
                'submittal_number' => 'SUB-001',
                'title' => 'Paint color samples',
                'created_by' => 1
            ]);
            CLI::write("Successfully created Submittal ID {$subId}", 'green');
        } catch (\Exception $e) {
            CLI::error("Error inserting Submittal: " . $e->getMessage());
            return;
        }

        // 4. Create a Drawing
        try {
            $dwgId = $drawings->insert([
                'project_id' => $projectId,
                'tenant_id' => 1,
                'branch_id' => 10,
                'discipline' => 'Architectural',
                'drawing_number' => 'A101',
                'title' => 'Floor Plan',
                'revision' => '0',
                'created_by' => 1
            ]);
            CLI::write("Successfully created Drawing ID {$dwgId}", 'green');
        } catch (\Exception $e) {
            CLI::error("Error inserting Drawing: " . $e->getMessage());
            return;
        }
        
        // 5. Verify Branch Enforcement error when missing branch_id
        try {
            $failDwgId = $drawings->insert([
                'project_id' => $projectId,
                'tenant_id' => 1,
                // MISSING branch_id
                'discipline' => 'Architectural',
                'drawing_number' => 'A102',
                'title' => 'Roof Plan',
                'revision' => '0',
                'created_by' => 1
            ]);
            CLI::error("FAILED: Expected validation exception for missing branch_id.");
            return;
        } catch (\Exception $e) {
            CLI::write("Successfully caught missing branch_id exception: " . $e->getMessage(), 'green');
        }

        CLI::write("SUCCESS: Phase 5 data linkage and schema verification passed.", 'black', 'green');

        // Cleanup test data
        $drawings->delete($dwgId, true);
        $submittals->delete($subId, true);
        $rfis->delete($rfiId, true);
        $projects->delete($projectId, true);
        CLI::write("Cleanup completed.", 'yellow');
    }
}
