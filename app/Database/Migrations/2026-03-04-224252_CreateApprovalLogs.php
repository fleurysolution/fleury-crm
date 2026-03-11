<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalLogs extends Migration
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
            'request_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'step_no' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, // System logs might have null
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 50, // e.g. 'approved', 'rejected', 'escalated', 'delegated'
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('request_id', 'fs_as_approval_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'fs_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('fs_approval_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_approval_logs', true);
    }
}
