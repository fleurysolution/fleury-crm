<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFilesCommentsTable extends Migration
{
    public function up(): void
    {
        // project_files — file uploads scoped to a project
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'   => ['type' => 'INT', 'unsigned' => true],
            'entity_type'  => ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true, 'comment' => 'task, rfi, submittal, etc.'],
            'entity_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => 'original filename'],
            'stored_name'  => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => 'unique filename on disk'],
            'path'         => ['type' => 'VARCHAR', 'constraint' => 500],
            'mime_type'    => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'size'         => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'comment' => 'bytes'],
            'description'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'uploaded_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'entity_type', 'entity_id']);
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('project_files');

        // comments — generic comment thread on any entity
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'entity_type'  => ['type' => 'VARCHAR', 'constraint' => 80,  'comment' => 'task, rfi, submittal, punch_list, site_diary, contract, invoice'],
            'entity_id'    => ['type' => 'INT', 'unsigned' => true],
            'parent_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => 'for threaded replies'],
            'body'         => ['type' => 'TEXT'],
            'user_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['entity_type', 'entity_id', 'deleted_at']);
        $this->forge->addKey('project_id');
        $this->forge->createTable('comments');
    }

    public function down(): void
    {
        $this->forge->dropTable('comments', true);
        $this->forge->dropTable('project_files', true);
    }
}
