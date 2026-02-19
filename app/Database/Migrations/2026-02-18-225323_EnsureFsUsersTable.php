<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsureFsUsersTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('fs_users')) {
            return;
        }

        if ($db->tableExists('fs_as_users')) {
            // Rename fs_as_users to fs_users
            $this->forge->renameTable('fs_as_users', 'fs_users');
        } else {
            // Create fs_users
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'employee_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 190,
                    'null'       => false,
                ],
                'password_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => false,
                ],
                'first_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'last_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'avatar_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'active',
                    'null'       => false,
                ],
                'email_verified_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'last_login_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
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
            $this->forge->addUniqueKey('email');
            $this->forge->createTable('fs_users', true);
        }
    }

    public function down()
    {
        // We won't strictly reverse the rename because we want to stick with fs_users
        // But for completeness:
        // $this->forge->renameTable('fs_users', 'fs_as_users');
    }
}
