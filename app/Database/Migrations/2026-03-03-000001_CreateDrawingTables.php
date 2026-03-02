<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDrawingTables extends Migration
{
    public function up()
    {
        // project_drawings table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'drawing_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'discipline' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Current', 'Superseded', 'Pending'],
                'default'    => 'Current',
            ],
            'current_revision' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addKey('project_id');
        $this->forge->addKey('drawing_no');
        $this->forge->createTable('project_drawings', true);

        // project_drawing_revisions table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'drawing_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'revision_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'revision_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => 300,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'uploaded_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
    }

    public function down()
    {
        $this->forge->dropTable('project_drawing_revisions', true);
        $this->forge->dropTable('project_drawings', true);
    }
}
