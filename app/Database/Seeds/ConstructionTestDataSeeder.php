<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConstructionTestDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        // 0. Get Admin User
        $admin = $db->table('fs_users')->where('email', 'admin@bpms.com')->get()->getRow();
        if (!$admin) {
            $admin = $db->table('fs_users')->get()->getRow();
        }
        $adminId = $admin ? $admin->id : 1;

        // 1. Clients & Contacts
        $clients = [
            ['company_name' => 'Metro City Infrastructure', 'status' => 'active', 'type' => 'organization'],
            ['company_name' => 'Skyline Developers', 'status' => 'active', 'type' => 'organization'],
        ];
        foreach ($clients as $c) {
            if (!$db->table('clients')->where('company_name', $c['company_name'])->countAllResults()) {
                $db->table('clients')->insert(array_merge($c, ['created_at' => $now, 'updated_at' => $now, 'created_by' => $adminId]));
                $clientId = $db->insertID();
                
                $db->table('contacts')->insert([
                    'client_id' => $clientId,
                    'name'      => ($c['company_name'] === 'Metro City Infrastructure') ? 'John Doe' : 'Jane Smith',
                    'email'     => ($c['company_name'] === 'Metro City Infrastructure') ? 'john@metrocity.com' : 'jane@skylinedev.com',
                    'is_primary' => 1,
                    'created_at' => $now,
                ]);
            }
        }
        $clientList = $db->table('clients')->get()->getResult();

        // 2. Projects
        $projects = [
            [
                'title'       => 'Downtown Residential Tower',
                'client_id'   => $clientList[0]->id ?? null,
                'pm_user_id'  => $adminId,
                'status'      => 'open',
                'priority'    => 'high',
                'budget'      => 15000000,
                'currency'    => 'USD',
                'description' => 'Construction of a 40-story residential complex.',
                'color'       => '#4a90e2',
                'start_date'  => date('Y-m-d', strtotime('-2 months')),
                'end_date'    => date('Y-m-d', strtotime('+18 months')),
                'created_by'  => $adminId,
                'created_at'  => $now,
            ],
            [
                'title'       => 'Highway Bridge Expansion',
                'client_id'   => $clientList[1]->id ?? null,
                'pm_user_id'  => $adminId,
                'status'      => 'open',
                'priority'    => 'medium',
                'budget'      => 8500000,
                'currency'    => 'USD',
                'description' => 'Expanding existing infrastructure.',
                'color'       => '#27ae60',
                'start_date'  => date('Y-m-d', strtotime('-1 month')),
                'end_date'    => date('Y-m-d', strtotime('+12 months')),
                'created_by'  => $adminId,
                'created_at'  => $now,
            ],
        ];

        foreach ($projects as $p) {
            if (!$db->table('projects')->where('title', $p['title'])->countAllResults()) {
                $db->table('projects')->insert($p);
            }
        }
        $projectList = $db->table('projects')->get()->getResult();

        // 3. WBS & Tasks
        foreach ($projectList as $p) {
            // Phases
            $phases = [
                ['project_id' => $p->id, 'title' => 'Structural Works', 'sort_order' => 1, 'color' => '#f39c12'],
                ['project_id' => $p->id, 'title' => 'MEP Installations', 'sort_order' => 2, 'color' => '#e67e22'],
            ];
            foreach ($phases as $ph) {
                if (!$db->table('wbs_phases')->where(['project_id' => $p->id, 'title' => $ph['title']])->countAllResults()) {
                    $db->table('wbs_phases')->insert($ph);
                }
            }
            $phaseList = $db->table('wbs_phases')->where('project_id', $p->id)->get()->getResult();

            // Milestones
            $milestones = [
                ['project_id' => $p->id, 'title' => 'Foundation Completion', 'due_date' => date('Y-m-d', strtotime('+1 month')), 'status' => 'pending', 'is_client_facing' => 1],
            ];
            foreach ($milestones as $ms) {
                if (!$db->table('project_milestones')->where(['project_id' => $p->id, 'title' => $ms['title']])->countAllResults()) {
                    $db->table('project_milestones')->insert(array_merge($ms, ['created_at' => $now, 'created_by' => $adminId]));
                }
            }

            // Tasks
            $tasks = [
                ['project_id' => $p->id, 'phase_id' => $phaseList[0]->id ?? null, 'title' => 'Excavation', 'status' => 'completed', 'percent_complete' => 100, 'due_date' => date('Y-m-d', strtotime('-1 week')), 'priority' => 'medium', 'created_by' => $adminId, 'created_at' => $now],
                ['project_id' => $p->id, 'phase_id' => $phaseList[0]->id ?? null, 'title' => 'Piling', 'status' => 'in_progress', 'percent_complete' => 45, 'due_date' => date('Y-m-d', strtotime('+2 weeks')), 'priority' => 'high', 'created_by' => $adminId, 'created_at' => $now],
            ];
            foreach ($tasks as $ts) {
                if (!$db->table('tasks')->where(['project_id' => $p->id, 'title' => $ts['title']])->countAllResults()) {
                    $db->table('tasks')->insert($ts);
                }
            }
        }

        // 4. Quality (RFIs, Submittals, Punch List)
        foreach ($projectList as $p) {
            // RFIs
            $rfis = [
                ['project_id' => $p->id, 'title' => 'Rebar Specs Clarification', 'description' => 'Specs query.', 'status' => 'open', 'priority' => 'high', 'discipline' => 'Structural', 'rfi_number' => 'RFI-'.$p->id.'-001', 'submitted_by' => $adminId, 'created_at' => $now],
            ];
            foreach ($rfis as $r) {
                if (!$db->table('rfis')->where(['project_id' => $p->id, 'title' => $r['title']])->countAllResults()) {
                    $db->table('rfis')->insert($r);
                }
            }

            // Submittals
            $submittals = [
                ['project_id' => $p->id, 'title' => 'Concrete Design', 'spec_section' => '033000', 'type' => 'shop_drawing', 'status' => 'approved', 'due_date' => $today, 'submittal_number' => 'SUB-'.$p->id.'-001', 'submitted_by' => $adminId, 'created_at' => $now],
            ];
            foreach ($submittals as $s) {
                if (!$db->table('submittals')->where(['project_id' => $p->id, 'title' => $s['title']])->countAllResults()) {
                    $db->table('submittals')->insert($s);
                }
            }

            // Punch List
            $punch = [
                ['project_id' => $p->id, 'title' => 'Paint touch-up', 'trade' => 'Painting', 'status' => 'open', 'priority' => 'low', 'reported_by' => $adminId, 'created_at' => $now, 'due_date' => $today],
            ];
            foreach ($punch as $item) {
                if (!$db->table('punch_list_items')->where(['project_id' => $p->id, 'title' => $item['title']])->countAllResults()) {
                    $db->table('punch_list_items')->insert($item);
                }
            }
        }

        // 5. Site & Timesheets
        foreach ($projectList as $p) {
            $diaryDate = date('Y-m-d', strtotime('-1 day'));
            if (!$db->table('site_diary_entries')->where(['project_id' => $p->id, 'entry_date' => $diaryDate])->countAllResults()) {
                $db->table('site_diary_entries')->insert([
                    'project_id' => $p->id,
                    'entry_date' => $diaryDate,
                    'weather'    => 'Sunny',
                    'temperature' => '25C',
                    'status'     => 'approved',
                    'created_by' => $adminId,
                    'created_at' => $now,
                ]);
            }
        }

        // Timesheets
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        if (!$db->table('timesheets')->where(['user_id' => $adminId, 'week_start' => $weekStart])->countAllResults()) {
            $tsId = $db->table('timesheets')->insert([
                'user_id'    => $adminId,
                'week_start' => $weekStart,
                'status'     => 'submitted',
                'created_at' => $now,
            ]);
            
            $task = $db->table('tasks')->where('project_id', $projectList[0]->id)->get()->getRow();
            if ($task) {
                $db->table('timesheet_entries')->insert([
                    'timesheet_id' => $tsId,
                    'project_id'   => $projectList[0]->id,
                    'task_id'      => $task->id,
                    'entry_date'   => $today,
                    'hours'        => 8,
                    'created_at'   => $now,
                ]);
            }
        }

        // 6. Finance
        foreach ($projectList as $p) {
            if (!$db->table('project_contracts')->where('project_id', $p->id)->countAllResults()) {
                $db->table('project_contracts')->insert([
                    'project_id' => $p->id,
                    'title'      => 'Main Contract',
                    'contract_number' => 'C-'.$p->id,
                    'type'       => 'main',
                    'status'     => 'signed',
                    'value'      => $p->budget ?? 0,
                    'currency'   => 'USD',
                    'created_by' => $adminId,
                    'created_at' => $now,
                ]);
            }

            if (!$db->table('project_invoices')->where('project_id', $p->id)->countAllResults()) {
                $db->table('project_invoices')->insert([
                    'project_id'     => $p->id,
                    'party_name'     => $clientList[0]->company_name ?? 'Test Client',
                    'invoice_number' => 'INV-'.$p->id,
                    'invoice_date'   => $today,
                    'subtotal'       => 10000,
                    'total_amount'   => 10000,
                    'status'         => 'draft',
                    'created_at'     => $now,
                ]);
            }
        }

        echo "Construction test data seeded successfully!\n";
    }
}
