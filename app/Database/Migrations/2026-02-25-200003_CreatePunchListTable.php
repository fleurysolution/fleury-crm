<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePunchListTable extends Migration
{
    public function up(): void
    {
        // Punch list items
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'  => ['type' => 'INT', 'unsigned' => true],
            'area_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'task_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'item_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'trade'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'      => ['type' => 'ENUM', 'constraint' => ['open','in_progress','resolved','closed','voided'], 'default' => 'open'],
            'priority'    => ['type' => 'ENUM', 'constraint' => ['low','medium','high','urgent'], 'default' => 'medium'],
            'reported_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'assigned_to' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'due_date'    => ['type' => 'DATE', 'null' => true],
            'resolved_at' => ['type' => 'DATETIME', 'null' => true],
            'closed_at'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('punch_list_items');

        // Punch photos
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'punch_item_id' => ['type' => 'INT', 'unsigned' => true],
            'filepath'      => ['type' => 'VARCHAR', 'constraint' => 500],
            'caption'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['punch_item_id']);
        $this->forge->createTable('punch_list_photos');
    }

    public function down(): void
    {
        $this->forge->dropTable('punch_list_photos', true);
        $this->forge->dropTable('punch_list_items', true);
    }
}
