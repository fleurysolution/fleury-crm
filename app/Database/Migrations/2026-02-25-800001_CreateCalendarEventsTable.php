<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCalendarEventsTable extends Migration
{
    public function up(): void
    {
        // calendar_events — custom events & meetings (not auto-generated from tasks/milestones)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'type'         => ['type' => 'ENUM', 'constraint' => ['meeting','inspection','deadline','reminder','other'], 'default' => 'other'],
            'start_date'   => ['type' => 'DATETIME'],
            'end_date'     => ['type' => 'DATETIME', 'null' => true],
            'all_day'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'location'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'color'        => ['type' => 'VARCHAR', 'constraint' => 20,  'default' => '#3b82f6'],
            'assigned_to'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_by'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'start_date']);
        $this->forge->addKey('assigned_to');
        $this->forge->createTable('calendar_events');
    }

    public function down(): void
    {
        $this->forge->dropTable('calendar_events', true);
    }
}
