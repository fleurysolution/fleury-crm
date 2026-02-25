<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAreaAndCostCodeTables extends Migration
{
    public function up(): void
    {
        // ── areas ────────────────────────────────────────────────────────
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'  => ['type' => 'INT', 'unsigned' => true],
            'parent_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'type'        => ['type' => 'ENUM', 'constraint' => ['building','floor','zone','unit','other'], 'default' => 'other'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addKey('parent_id');
        $this->forge->createTable('areas', true);

        // ── cost_codes ───────────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],  // null = global/org-wide
            'parent_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'category'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->createTable('cost_codes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('cost_codes', true);
        $this->forge->dropTable('areas', true);
    }
}
