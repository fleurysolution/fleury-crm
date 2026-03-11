<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMeetingTables extends Migration
{
    public function up()
    {
        // 1. Meetings Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'meeting_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'meeting_time' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'agenda' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'minutes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'scheduled', 
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['tenant_id', 'project_id']);
        $this->forge->createTable('meetings');

        // 2. Meeting Attendees Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'meeting_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'invited', // invited, present, absent
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['meeting_id', 'user_id']);
        $this->forge->createTable('meeting_attendees');
    }

    public function down()
    {
        $this->forge->dropTable('meeting_attendees');
        $this->forge->dropTable('meetings');
    }
}
