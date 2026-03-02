<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDrawingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'discipline' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'drawing_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'revision' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => '0',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['current', 'superseded', 'draft'],
                'default'    => 'current',
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('drawings');
    }

    public function down()
    {
        $this->forge->dropTable('drawings', true);
    }
}
