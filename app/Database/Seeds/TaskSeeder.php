<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $project = $db->table('projects')->like('title', 'Commercial Office Fit-Out')->get()->getRow();
        
        if (!$project) {
            echo "Project not found.\n";
            return;
        }
        
        $milestone = $db->table('project_milestones')->where('project_id', $project->id)->get()->getRow();
        $milestoneId = $milestone ? $milestone->id : null;

        $tasks = [
            ['title' => 'Site Mobilization and Setup', 'points' => 5],
            ['title' => 'Demolition of existing partitions', 'points' => 8],
            ['title' => 'HVAC Ductwork Rough-in', 'points' => 10],
            ['title' => 'Electrical First Fix (Wiring)', 'points' => 12],
            ['title' => 'Plumbing First Fix', 'points' => 6],
            ['title' => 'Data and Comms Cabling', 'points' => 8],
            ['title' => 'Partition Wall Framing', 'points' => 15],
            ['title' => 'Drywall Installation', 'points' => 15],
            ['title' => 'Ceiling Grid Installation', 'points' => 10],
            ['title' => 'Painting - Base Coat', 'points' => 8],
            ['title' => 'Lighting Fixture Installation', 'points' => 5],
            ['title' => 'HVAC Diffusers and Grilles', 'points' => 4],
            ['title' => 'Flooring Preparation', 'points' => 5],
            ['title' => 'Carpet Tile Installation', 'points' => 10],
            ['title' => 'Glass Partition Installation', 'points' => 15],
            ['title' => 'Millwork and Cabinetry Install', 'points' => 12],
            ['title' => 'Electrical Second Fix (Switches/Plugs)', 'points' => 8],
            ['title' => 'Painting - Final Coat', 'points' => 8],
            ['title' => 'Final Cleaning and Snagging', 'points' => 5],
            ['title' => 'Client Handover Inspection', 'points' => 3],
        ];

        $insertData = [];
        $now = date('Y-m-d H:i:s');
        
        $i = 1;
        foreach ($tasks as $t) {
            $insertData[] = [
                'project_id'   => $project->id,
                'milestone_id' => $milestoneId,
                'title'        => $t['title'],
                'points'       => $t['points'],
                'status'       => $i <= 3 ? 'done' : ($i <= 6 ? 'in_progress' : 'todo'),
                'start_date'   => date('Y-m-d', strtotime("+$i days")),
                'due_date'     => date('Y-m-d', strtotime("+" . ($i + 2) . " days")),
                'created_by'   => 1, // Default user
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
            $i++;
        }

        $db->table('tasks')->insertBatch($insertData);
        echo "Successfully inserted 20 tasks for project: " . $project->title . "\n";
    }
}
