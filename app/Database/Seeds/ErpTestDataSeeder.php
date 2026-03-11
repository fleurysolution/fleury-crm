<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ErpTestDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Ensure a Default Tenant exists
        $tenantId = 1;
        $tenant = $db->table('tenants')->where('id', $tenantId)->get()->getRow();
        if (!$tenant) {
            $db->table('tenants')->insert([
                'id' => $tenantId,
                'name' => 'Default Organization',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Ensure Regions from the user's list exist
        $regionNames = ['Corporate', 'Northeast', 'Southeast', 'Southwest', 'West Coast'];
        $regionsMap = [];
        foreach ($regionNames as $idx => $rName) {
            $regionData = $db->table('regions')->where('name', $rName)->get()->getRow();
            if (!$regionData) {
                $db->table('regions')->insert([
                    'tenant_id' => $tenantId,
                    'name' => $rName,
                    'code' => strtoupper(substr(str_replace(' ', '', $rName), 0, 3)),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $regionsMap[$rName] = $db->insertID();
            } else {
                $regionsMap[$rName] = $regionData->id;
            }
        }

        // 3. Branches from User Request
        $branches = [
            ['id' => 8,  'name' => 'Corporate HQ (8)', 'region' => 'Corporate', 'address' => 'TBD - Corporate HQ address, Conyers, GA (8)', 'phone' => '000-000-0000 (8)', 'email' => 'corp-hq@bpms247.com (8)'],
            ['id' => 9,  'name' => 'Logistics HQ (9)', 'region' => 'Corporate', 'address' => 'TBD - Logistics address (9)', 'phone' => '000-000-0000 (9)', 'email' => 'corp-logistics@bpms247.com (9)'],
            ['id' => 10, 'name' => 'Hartford CT (10)', 'region' => 'Northeast', 'address' => 'TBD - address, Hartford, CT (10)', 'phone' => '000-000-0000 (10)', 'email' => 'ne-hartford@bpms247.com (10)'],
            ['id' => 11, 'name' => 'Boston MA (11)', 'region' => 'Northeast', 'address' => 'TBD - address, Boston, MA (11)', 'phone' => '000-000-0000 (11)', 'email' => 'ne-boston@bpms247.com (11)'],
            ['id' => 12, 'name' => 'Columbus OH (12)', 'region' => 'Northeast', 'address' => 'TBD - address, Columbus, OH (12)', 'phone' => '000-000-0000 (12)', 'email' => 'ne-columbus@bpms247.com (12)'],
            ['id' => 13, 'name' => 'Pennsylvania (Hub) (13)', 'region' => 'Northeast', 'address' => 'TBD - address, Pennsylvania, PA (13)', 'phone' => '000-000-0000 (13)', 'email' => 'ne-pennsylvania@bpms247.com (13)'],
            ['id' => 14, 'name' => 'Providence RI (14)', 'region' => 'Northeast', 'address' => 'TBD - address, Providence, RI (14)', 'phone' => '000-000-0000 (14)', 'email' => 'ne-providence@bpms247.com (14)'],
            ['id' => 15, 'name' => 'Washington DC (15)', 'region' => 'Northeast', 'address' => 'TBD - address, Washington, DC (15)', 'phone' => '000-000-0000 (15)', 'email' => 'ne-dc@bpms247.com (15)'],
            ['id' => 16, 'name' => 'Miami FL (16)', 'region' => 'Southeast', 'address' => 'TBD - address, Miami, FL (16)', 'phone' => '000-000-0000 (16)', 'email' => 'se-miami@bpms247.com (16)'],
            ['id' => 17, 'name' => 'Atlanta GA (17)', 'region' => 'Southeast', 'address' => 'TBD - address, Atlanta, GA (17)', 'phone' => '000-000-0000 (17)', 'email' => 'se-atlanta@bpms247.com (17)'],
            ['id' => 18, 'name' => 'Jackson MS (18)', 'region' => 'Southeast', 'address' => 'TBD - address, Jackson, MS (18)', 'phone' => '000-000-0000 (18)', 'email' => 'se-jackson@bpms247.com (18)'],
            ['id' => 19, 'name' => 'Charlotte NC (19)', 'region' => 'Southeast', 'address' => 'TBD - address, Charlotte, NC (19)', 'phone' => '000-000-0000 (19)', 'email' => 'se-charlotte@bpms247.com (19)'],
            ['id' => 20, 'name' => 'Raleigh NC (20)', 'region' => 'Southeast', 'address' => 'TBD - address, Raleigh, NC (20)', 'phone' => '000-000-0000 (20)', 'email' => 'se-raleigh@bpms247.com (20)'],
            ['id' => 21, 'name' => 'Chattanooga TN (21)', 'region' => 'Southeast', 'address' => 'TBD - address, Chattanooga, TN (21)', 'phone' => '000-000-0000 (21)', 'email' => 'se-chattanooga@bpms247.com (21)'],
            ['id' => 22, 'name' => 'A1 Lift Rentals TN (22)', 'region' => 'Southeast', 'address' => 'TBD - address, Tennessee (22)', 'phone' => '000-000-0000 (22)', 'email' => 'se-a1lift-tn@bpms247.com (22)'],
            ['id' => 23, 'name' => 'Lafayette LA (23)', 'region' => 'Southwest', 'address' => 'TBD - address, Lafayette, LA (23)', 'phone' => '000-000-0000 (23)', 'email' => 'sw-lafayette@bpms247.com (23)'],
            ['id' => 24, 'name' => 'Monroe LA (24)', 'region' => 'Southwest', 'address' => 'TBD - address, Monroe, LA (24)', 'phone' => '000-000-0000 (24)', 'email' => 'sw-monroe@bpms247.com (24)'],
            ['id' => 25, 'name' => 'Dallas TX (25)', 'region' => 'Southwest', 'address' => 'TBD - address, Dallas, TX (25)', 'phone' => '000-000-0000 (25)', 'email' => 'sw-dallas@bpms247.com (25)'],
            ['id' => 26, 'name' => 'Houston TX (26)', 'region' => 'Southwest', 'address' => 'TBD - address, Houston, TX (26)', 'phone' => '000-000-0000 (26)', 'email' => 'sw-houston@bpms247.com (26)'],
            ['id' => 27, 'name' => 'San Antonio TX (27)', 'region' => 'Southwest', 'address' => 'TBD - address, San Antonio, TX (27)', 'phone' => '000-000-0000 (27)', 'email' => 'sw-sanantonio@bpms247.com (27)'],
            ['id' => 28, 'name' => 'Phoenix AZ (28)', 'region' => 'West Coast', 'address' => 'TBD - address, Phoenix, AZ (28)', 'phone' => '000-000-0000 (28)', 'email' => 'wc-phoenix@bpms247.com (28)'],
            ['id' => 29, 'name' => 'Los Angeles CA (29)', 'region' => 'West Coast', 'address' => 'TBD - address, Los Angeles, CA (29)', 'phone' => '000-000-0000 (29)', 'email' => 'wc-losangeles@bpms247.com (29)'],
            ['id' => 30, 'name' => 'Reno NV (30)', 'region' => 'West Coast', 'address' => 'TBD - address, Reno, NV (30)', 'phone' => '000-000-0000 (30)', 'email' => 'wc-reno@bpms247.com (30)'],
            ['id' => 31, 'name' => 'Salt Lake City UT (31)', 'region' => 'West Coast', 'address' => 'TBD - address, Salt Lake City, UT (31)', 'phone' => '000-000-0000 (31)', 'email' => 'wc-saltlake@bpms247.com (31)'],
            ['id' => 32, 'name' => 'Portland OR (32)', 'region' => 'West Coast', 'address' => 'TBD - address, Portland, OR (32)', 'phone' => '000-000-0000 (32)', 'email' => 'wc-portland@bpms247.com (32)']
        ];

        foreach ($branches as $b) {
            $exists = $db->table('branches')->where('id', $b['id'])->countAllResults();
            if ($exists == 0) {
                // Also insert into offices table for backwards compatibility with UI
                $db->table('offices')->insert([
                    'id' => $b['id'],
                    'region_id' => $regionsMap[$b['region']],
                    'name' => $b['name'],
                    'address' => $b['address'],
                    'phone' => $b['phone'],
                    'email' => $b['email'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // Insert into new branches table
                $db->table('branches')->insert([
                    'id' => $b['id'],
                    'tenant_id' => $tenantId,
                    'region_id' => $regionsMap[$b['region']],
                    'name' => $b['name'],
                    'address' => $b['address'],
                    'phone' => $b['phone'],
                    'email' => $b['email'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 4. Test Roles
        $roles = [
            ['slug' => 'pm', 'name' => 'Project Manager'],
            ['slug' => 'superintendent', 'name' => 'Superintendent'],
            ['slug' => 'engineer', 'name' => 'Site Engineer'],
            ['slug' => 'foreman', 'name' => 'Foreman']
        ];
        foreach ($roles as $role) {
            $exists = $db->table('roles')->where('slug', $role['slug'])->countAllResults();
            if ($exists == 0) {
                $db->table('roles')->insert([
                    'slug' => $role['slug'],
                    'name' => $role['name'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 5. Test Departments (Teams)
        $departments = ['Construction', 'Operations', 'Finance', 'Procurement', 'Safety'];
        foreach ($branches as $b) {
            foreach ($departments as $dept) {
                $exists = $db->table('departments')->where('branch_id', $b['id'])->where('name', $dept)->countAllResults();
                if ($exists == 0) {
                    $db->table('departments')->insert([
                        'tenant_id' => $tenantId,
                        'branch_id' => $b['id'],
                        'name' => $dept,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        // 6. Test Projects (Assign one to each branch for testing)
        // Just create some dummy projects for the first few branches
        $testProjects = [
            ['title' => 'Conyers Office Fitout', 'branch_id' => 8, 'budget' => 500000, 'stage' => 'active', 'contract' => 'lump_sum'],
            ['title' => 'Hartford Bridge Repair', 'branch_id' => 10, 'budget' => 2500000, 'stage' => 'pre_construction', 'contract' => 'unit_price'],
            ['title' => 'Miami Highrise Phase 1', 'branch_id' => 16, 'budget' => 12000000, 'stage' => 'bidding', 'contract' => 'cost_plus'],
            ['title' => 'Dallas Commercial Park', 'branch_id' => 25, 'budget' => 8500000, 'stage' => 'active', 'contract' => 'lump_sum'],
            ['title' => 'Seattle Warehouse Expansion', 'branch_id' => 32, 'budget' => 450000, 'stage' => 'closeout', 'contract' => 'lump_sum'],
        ];

        foreach ($testProjects as $tp) {
            $exists = $db->table('projects')->where('title', $tp['title'])->where('branch_id', $tp['branch_id'])->countAllResults();
            if ($exists == 0) {
                $db->table('projects')->insert([
                    'title' => $tp['title'],
                    'tenant_id' => $tenantId,
                    'branch_id' => $tp['branch_id'],
                    'budget' => $tp['budget'],
                    'versioned_budget_baseline' => $tp['budget'],
                    'contract_type' => $tp['contract'],
                    'project_stage' => $tp['stage'],
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        echo "Successfully seeded ERP test data (Branches, Roles, Departments, Projects).\n";
    }
}
