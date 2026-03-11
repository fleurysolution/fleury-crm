<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDrawingTables extends Migration
{
    public function up()
    {
        // 1. fs_drawings
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
            'discipline' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // Architectural, Structural, etc.
            ],
            'drawing_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'current_revision_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'tenant_id']);
        $this->forge->createTable('fs_drawings', true);

        // 2. project_drawing_revisions
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'drawing_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'revision_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('drawing_id');
        $this->forge->createTable('project_drawing_revisions', true);

        // 3. drawing_pins (Markups)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'drawing_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'revision_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pos_x' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', // Percentage based 0-100
            ],
            'pos_y' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'pin_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // rfi, observation, note
            ],
            'reference_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('drawing_id');
        $this->forge->createTable('drawing_pins', true);
    }

    public function down()
    {
        $this->forge->dropTable('drawing_pins');
        $this->forge->dropTable('project_drawing_revisions');
        $this->forge->dropTable('fs_drawings');
    }
}
