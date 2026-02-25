<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteDiaryTable extends Migration
{
    public function up(): void
    {
        // Site diary entries (daily logs)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'     => ['type' => 'INT', 'unsigned' => true],
            'entry_date'     => ['type' => 'DATE'],
            'weather'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'temperature'    => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => true],
            'manpower_count' => ['type' => 'INT', 'default' => 0],
            'working_hours'  => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['draft','submitted','approved'], 'default' => 'draft'],
            'created_by'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['project_id', 'entry_date']);
        $this->forge->createTable('site_diary_entries');

        // Site diary line items (progress notes, issues, incidents, visits)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'diary_id'    => ['type' => 'INT', 'unsigned' => true],
            'type'        => ['type' => 'ENUM', 'constraint' => ['progress','issue','delay','incident','visitor','equipment'], 'default' => 'progress'],
            'description' => ['type' => 'TEXT'],
            'area_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'task_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['diary_id']);
        $this->forge->createTable('site_diary_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('site_diary_items', true);
        $this->forge->dropTable('site_diary_entries', true);
    }
}
