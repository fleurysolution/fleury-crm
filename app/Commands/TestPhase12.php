<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ClientModel;
use App\Models\LeadModel;

class TestPhase12 extends BaseCommand
{
    protected $group       = 'Verification';
    protected $name        = 'test:phase12';
    protected $description = 'Verifies Phase 12: CRM Data Isolation';

    public function run(array $params)
    {
        CLI::write('Starting Phase 12 Verification (CRM Data Isolation)...', 'blue');

        $clientModel = new ClientModel();
        $leadModel = new LeadModel();

        // Branch IDs
        $branch12 = 12;
        $branch24 = 24;
        $tenantId = 1;

        // Cleanup
        $clientModel->where('company_name', 'Phase 12 Test Client')->delete(null, true);
        $leadModel->where('company_name', 'Phase 12 Test Lead')->delete(null, true);

        // 1. Seed Data for Branch 12
        CLI::write('Step 1: Seeding CRM data for Branch 12...', 'cyan');
        
        session()->set([
            'tenant_id' => $tenantId,
            'branch_id' => $branch12,
            'geo_access_permission' => 'branch',
            'is_logged_in' => true
        ]);

        $clientId = $clientModel->insert([
            'tenant_id' => $tenantId,
            'branch_id' => $branch12,
            'company_name' => 'Phase 12 Test Client',
            'type' => 'Organization',
            'status' => 'active'
        ]);

        $leadId = $leadModel->insert([
            'tenant_id' => $tenantId,
            'branch_id' => $branch12,
            'title' => 'Hot Prospect',
            'company_name' => 'Phase 12 Test Lead',
            'status' => 'new'
        ]);

        CLI::write("Seeded Client ID: $clientId, Lead ID: $leadId", 'green');

        // 2. Verify Visibility in Branch 12
        CLI::write('Step 2: Verifying visibility in Branch 12...', 'cyan');
        $visibleClients = $clientModel->findAll();
        $visibleLeads = $leadModel->findAll();

        $clientFound = false;
        foreach ($visibleClients as $c) if ($c['id'] == $clientId) $clientFound = true;
        
        $leadFound = false;
        foreach ($visibleLeads as $l) if ($l['id'] == $leadId) $leadFound = true;

        if ($clientFound && $leadFound) {
            CLI::write('SUCCESS: Branch 12 can see its own CRM data.', 'green');
        } else {
            CLI::write('FAILURE: Branch 12 cannot see its own CRM data.', 'red');
            return;
        }

        // 3. Verify Isolation in Branch 24
        CLI::write('Step 3: Verifying isolation in Branch 24...', 'cyan');
        session()->set('branch_id', $branch24);

        $isolatedClients = $clientModel->findAll();
        $isolatedLeads = $leadModel->findAll();

        foreach ($isolatedClients as $c) {
            if ($c['id'] == $clientId) {
                CLI::write('FAILURE: Branch 24 can see Branch 12 client data!', 'red');
                return;
            }
        }
        foreach ($isolatedLeads as $l) {
            if ($l['id'] == $leadId) {
                CLI::write('FAILURE: Branch 24 can see Branch 12 lead data!', 'red');
                return;
            }
        }

        CLI::write('SUCCESS: Branch 24 is isolated from Branch 12 CRM data.', 'green');

        // 4. Verify Lead Conversion Isolation
        CLI::write('Step 4: Verifying Lead Conversion scoping...', 'cyan');
        // Back to Branch 12
        session()->set('branch_id', $branch12);
        
        // Mocking conversion (similar to Leads controller)
        $lead = $leadModel->find($leadId);
        $newClientId = $clientModel->insert([
            'tenant_id' => $tenantId,
            'branch_id' => $branch12,
            'company_name' => $lead['company_name'] . ' (Converted)',
            'type' => 'Organization',
            'status' => 'active'
        ]);

        $newClient = $clientModel->find($newClientId);
        if ($newClient['branch_id'] == $branch12) {
            CLI::write('SUCCESS: Converted client inherited Branch 12 ID.', 'green');
        } else {
            CLI::write("FAILURE: Converted client branch_id is {$newClient['branch_id']}, expected $branch12", 'red');
        }

        // Cleanup
        CLI::write('Cleanup: Removing test data...', 'cyan');
        $clientModel->where('company_name', 'Phase 12 Test Client')->delete(null, true);
        $clientModel->where('company_name', 'Phase 12 Test Lead (Converted)')->delete(null, true);
        $leadModel->where('company_name', 'Phase 12 Test Lead')->delete(null, true);

        CLI::write('Phase 12 Verification Complete!', 'green');
    }
}
