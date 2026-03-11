<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAutomationTables extends Migration
{
    public function up()
    {
        // 1. Automation Rules Table
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'trigger_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // e.g., 'on_record_create', 'on_status_change'
            ],
            'trigger_object' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // e.g., 'projects', 'change_orders', 'leads'
            ],
            'conditions' => [
                'type' => 'TEXT',
                'null' => true, // JSON encoded conditions
            ],
            'action_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // e.g., 'send_email', 'create_task', 'update_field'
            ],
            'action_config' => [
                'type' => 'TEXT',
                'null' => true, // JSON encoded configuration
            ],
            'is_active' => [
                'type'    => 'TINYINT',
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
        $this->forge->addKey('tenant_id');
        $this->forge->createTable('automation_rules');

        // 2. Automation Logs Table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'rule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'entity_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // success, failed
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'executed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['rule_id', 'tenant_id']);
        $this->forge->createTable('automation_logs');
    }

    public function down()
    {
        $this->forge->dropTable('automation_logs');
        $this->forge->dropTable('automation_rules');
    }
}
