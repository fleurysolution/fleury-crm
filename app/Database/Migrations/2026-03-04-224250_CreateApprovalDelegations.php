<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalDelegations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'delegator_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'delegatee_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('delegator_user_id', 'fs_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('delegatee_user_id', 'fs_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_approval_delegations', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_approval_delegations', true);
    }
}
