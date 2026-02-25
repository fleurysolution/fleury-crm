<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimesheetsTable extends Migration
{
    public function up(): void
    {
        // Timesheets (weekly containers)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'unsigned' => true],
            'week_start'   => ['type' => 'DATE'],
            'status'       => ['type' => 'ENUM', 'constraint' => ['draft','submitted','approved','rejected'], 'default' => 'draft'],
            'notes'        => ['type' => 'TEXT', 'null' => true],
            'submitted_at' => ['type' => 'DATETIME', 'null' => true],
            'approved_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at'  => ['type' => 'DATETIME', 'null' => true],
            'rejected_reason' => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'week_start']);
        $this->forge->createTable('timesheets');

        // Timesheet entries (daily rows)
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'timesheet_id'  => ['type' => 'INT', 'unsigned' => true],
            'project_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'task_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cost_code_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'area_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'entry_date'    => ['type' => 'DATE'],
            'hours'         => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'description'   => ['type' => 'TEXT', 'null' => true],
            'is_billable'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['timesheet_id']);
        $this->forge->addKey(['project_id']);
        $this->forge->addKey(['task_id']);
        $this->forge->createTable('timesheet_entries');

        // Resource assignments (planned hours per task per user)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_id'        => ['type' => 'INT', 'unsigned' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'planned_hours'  => ['type' => 'DECIMAL', 'constraint' => '7,2', 'default' => 0],
            'role'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['task_id', 'user_id']);
        $this->forge->createTable('resource_assignments');
    }

    public function down(): void
    {
        $this->forge->dropTable('resource_assignments', true);
        $this->forge->dropTable('timesheet_entries', true);
        $this->forge->dropTable('timesheets', true);
    }
}
