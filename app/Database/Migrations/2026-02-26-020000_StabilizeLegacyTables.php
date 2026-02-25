<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StabilizeLegacyTables extends Migration
{
    public function up()
    {
        // ── projects stabilization ─────────────────────────────────────
        $projectFields = [
            'pm_user_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'client_id'],
            'priority'    => ['type' => 'ENUM', 'constraint' => ['low','medium','high','urgent'], 'default' => 'medium', 'after' => 'status'],
            'end_date'    => ['type' => 'DATE', 'null' => true, 'after' => 'start_date'],
            'budget'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true, 'after' => 'end_date'],
            'currency'    => ['type' => 'CHAR', 'constraint' => 3, 'default' => 'USD', 'after' => 'budget'],
            'region_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'description'],
            'office_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'region_id'],
            'color'       => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#4a90e2', 'after' => 'office_id'],
            'created_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'color'],
        ];

        // Check if color exists before adding (safety)
        if (!$this->db->fieldExists('color', 'projects')) {
            $this->forge->addColumn('projects', $projectFields);
        }

        // ── tasks stabilization ────────────────────────────────────────
        $taskFields = [
            'phase_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'project_id'],
            'parent_task_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'phase_id'],
            'milestone_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'parent_task_id'],
            'area_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'milestone_id'],
            'cost_code_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'area_id'],
            'priority'         => ['type' => 'ENUM', 'constraint' => ['low','medium','high','urgent'], 'default' => 'medium', 'after' => 'status'],
            'due_date'         => ['type' => 'DATE', 'null' => true, 'after' => 'start_date'],
            'estimated_hours'  => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true, 'after' => 'due_date'],
            'actual_hours'     => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => '0.00', 'after' => 'estimated_hours'],
            'percent_complete' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0, 'after' => 'actual_hours'],
            'sort_order'       => ['type' => 'INT', 'default' => 0, 'after' => 'percent_complete'],
        ];

        if (!$this->db->fieldExists('priority', 'tasks')) {
            $this->forge->addColumn('tasks', $taskFields);
        }
    }

    public function down()
    {
        // No down migration for stabilization to prevent data loss on legacy tables
    }
}
