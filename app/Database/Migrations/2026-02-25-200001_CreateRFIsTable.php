<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRFIsTable extends Migration
{
    public function up(): void
    {
        // RFIs
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'      => ['type' => 'INT', 'unsigned' => true],
            'rfi_number'      => ['type' => 'VARCHAR', 'constraint' => 30],
            'title'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'discipline'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['draft','submitted','under_review','answered','closed'], 'default' => 'draft'],
            'priority'        => ['type' => 'ENUM', 'constraint' => ['low','medium','high','urgent'], 'default' => 'medium'],
            'submitted_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'assigned_to'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'area_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'task_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'due_date'        => ['type' => 'DATE', 'null' => true],
            'answered_at'     => ['type' => 'DATETIME', 'null' => true],
            'cost_impact'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'schedule_impact' => ['type' => 'INT', 'default' => 0, 'comment' => 'days'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('rfis');

        // RFI Responses
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'rfi_id'      => ['type' => 'INT', 'unsigned' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'body'        => ['type' => 'TEXT'],
            'attachments' => ['type' => 'JSON', 'null' => true],
            'is_official' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['rfi_id']);
        $this->forge->createTable('rfi_responses');
    }

    public function down(): void
    {
        $this->forge->dropTable('rfi_responses', true);
        $this->forge->dropTable('rfis', true);
    }
}
