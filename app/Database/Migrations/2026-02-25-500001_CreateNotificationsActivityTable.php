<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsActivityTable extends Migration
{
    public function up(): void
    {
        // notifications – in-app user notifications
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true, 'comment' => 'recipient'],
            'type'        => ['type' => 'VARCHAR', 'constraint' => 80,  'comment' => 'task_assigned, rfi_created, etc.'],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'body'        => ['type' => 'TEXT', 'null' => true],
            'url'         => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true, 'comment' => 'FontAwesome class'],
            'color'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true],
            'related_type'=> ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true, 'comment' => 'project, task, rfi, etc.'],
            'related_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'is_read'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'read_at'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'is_read']);
        $this->forge->createTable('notifications');

        // activity_log – project/entity audit trail
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'entity_type'  => ['type' => 'VARCHAR', 'constraint' => 80,  'comment' => 'task, rfi, contract, etc.'],
            'entity_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'action'       => ['type' => 'VARCHAR', 'constraint' => 80,  'comment' => 'created, updated, deleted, approved…'],
            'description'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'old_values'   => ['type' => 'JSON', 'null' => true],
            'new_values'   => ['type' => 'JSON', 'null' => true],
            'user_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'ip_address'   => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'entity_type', 'entity_id']);
        $this->forge->addKey(['user_id']);
        $this->forge->createTable('activity_log');
    }

    public function down(): void
    {
        $this->forge->dropTable('activity_log', true);
        $this->forge->dropTable('notifications', true);
    }
}
