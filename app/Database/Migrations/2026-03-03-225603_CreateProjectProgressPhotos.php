<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectProgressPhotos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'project_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'photo_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'caption'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'uploaded_by'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by', 'fs_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('project_progress_photos');
    }

    public function down()
    {
        $this->forge->dropTable('project_progress_photos');
    }
}
