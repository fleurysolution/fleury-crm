<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFsSettingsTable_Final extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'setting_name'  => ['type' => 'VARCHAR', 'constraint' => 191, 'unique' => true],
            'setting_value' => ['type' => 'TEXT', 'null' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fs_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_settings', true);
    }
}
