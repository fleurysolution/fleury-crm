<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectManagementTables extends Migration
{
    public function up(): void
    {
        // ── projects ────────────────────────────────────────────────────
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'           => ['type' => 'VARCHAR', 'constraint' => 200],
            'client_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'pm_user_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['draft','active','on_hold','completed','archived'], 'default' => 'draft'],
            'priority'        => ['type' => 'ENUM', 'constraint' => ['low','medium','high','urgent'], 'default' => 'medium'],
            'start_date'      => ['type' => 'DATE', 'null' => true],
            'end_date'        => ['type' => 'DATE', 'null' => true],
            'budget'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'currency'        => ['type' => 'CHAR', 'constraint' => 3, 'default' => 'USD'],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'region_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'office_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'color'           => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#4a90e2'],
            'created_by'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('client_id');
        $this->forge->addKey('status');
        $this->forge->createTable('projects', true);

        // ── wbs_phases ──────────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'unsigned' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'color'      => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#6c757d'],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->createTable('wbs_phases', true);

        // ── tasks ───────────────────────────────────────────────────────
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'       => ['type' => 'INT', 'unsigned' => true],
            'phase_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'parent_task_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'milestone_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'area_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cost_code_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title'            => ['type' => 'VARCHAR', 'constraint' => 500],
            'description'      => ['type' => 'TEXT', 'null' => true],
            'assigned_to'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['todo','in_progress','review','done','blocked'], 'default' => 'todo'],
            'priority'         => ['type' => 'ENUM', 'constraint' => ['low','medium','high','urgent'], 'default' => 'medium'],
            'start_date'       => ['type' => 'DATE', 'null' => true],
            'due_date'         => ['type' => 'DATE', 'null' => true],
            'estimated_hours'  => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'actual_hours'     => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => '0.00'],
            'percent_complete' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
            'sort_order'       => ['type' => 'INT', 'default' => 0],
            'created_by'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addKey('phase_id');
        $this->forge->addKey('assigned_to');
        $this->forge->addKey('status');
        $this->forge->createTable('tasks', true);

        // ── task_dependencies ───────────────────────────────────────────
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_id'         => ['type' => 'INT', 'unsigned' => true],
            'depends_on_task' => ['type' => 'INT', 'unsigned' => true],
            'type'            => ['type' => 'ENUM', 'constraint' => ['FS','SS','FF','SF'], 'default' => 'FS'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['task_id', 'depends_on_task']);
        $this->forge->createTable('task_dependencies', true);

        // ── project_milestones ──────────────────────────────────────────
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'          => ['type' => 'INT', 'unsigned' => true],
            'title'               => ['type' => 'VARCHAR', 'constraint' => 300],
            'description'         => ['type' => 'TEXT', 'null' => true],
            'due_date'            => ['type' => 'DATE', 'null' => true],
            'status'              => ['type' => 'ENUM', 'constraint' => ['pending','achieved','missed'], 'default' => 'pending'],
            'is_client_facing'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'acceptance_criteria' => ['type' => 'TEXT', 'null' => true],
            'created_by'          => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->createTable('project_milestones', true);

        // ── project_members ─────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'role'       => ['type' => 'ENUM', 'constraint' => ['pm','member','viewer','client','subcontractor'], 'default' => 'member'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'user_id']);
        $this->forge->createTable('project_members', true);

        // ── task_comments ───────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_id'    => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'parent_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'body'       => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id');
        $this->forge->createTable('task_comments', true);

        // ── task_attachments ────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_id'    => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'filename'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'filepath'   => ['type' => 'VARCHAR', 'constraint' => 500],
            'filesize'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'mime_type'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id');
        $this->forge->createTable('task_attachments', true);

        // ── task_checklists ─────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_id'    => ['type' => 'INT', 'unsigned' => true],
            'item_text'  => ['type' => 'VARCHAR', 'constraint' => 500],
            'is_done'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'done_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'done_at'    => ['type' => 'DATETIME', 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id');
        $this->forge->createTable('task_checklists', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('task_checklists', true);
        $this->forge->dropTable('task_attachments', true);
        $this->forge->dropTable('task_comments', true);
        $this->forge->dropTable('project_members', true);
        $this->forge->dropTable('project_milestones', true);
        $this->forge->dropTable('task_dependencies', true);
        $this->forge->dropTable('tasks', true);
        $this->forge->dropTable('wbs_phases', true);
        $this->forge->dropTable('projects', true);
    }
}
