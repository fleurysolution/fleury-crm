<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCybersecurityFields extends Migration
{
    public function up()
    {
        // Add fields to fs_users
        $fields = [
            'mfa_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'password_hash'
            ],
            'mfa_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'mfa_secret'
            ],
            'login_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'status'
            ],
            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'login_attempts'
            ],
            'last_ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'after' => 'last_login_at'
            ]
        ];
        
        // We use addColumn with caution in case they exist from previous failed runs
        // But since they failed before bootstrapping, they shouldn't exist.
        $this->forge->addColumn('fs_users', $fields);

        // Created security_log table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'severity' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'default' => 'low'
            ],
            'description' => [
                'type' => 'TEXT'
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45
            ],
            'user_agent' => [
                'type' => 'TEXT'
            ],
            'details' => [
                'type' => 'JSON',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('event_type');
        $this->forge->createTable('security_log', true);
    }

    public function down()
    {
        $this->forge->dropColumn('fs_users', ['mfa_secret', 'mfa_enabled', 'login_attempts', 'locked_until', 'last_ip_address']);
        $this->forge->dropTable('security_log', true);
    }
}
