<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomObjectsAndFields extends Migration
{
    public function up()
    {
        // 1. Custom Objects Table
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
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'label_singular' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'label_plural' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'active',
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
        $this->forge->createTable('custom_objects');

        // 2. Custom Fields Table
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
                'null'       => true,
            ],
            'object_type' => [
                'type'       => 'VARCHAR', // 'projects', 'clients', or name of custom object
                'constraint' => '100',
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'field_label' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'field_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // text, number, date, select
            ],
            'options' => [
                'type' => 'TEXT',
                'null' => true, // JSON or comma separated for select
            ],
            'is_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'active',
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
        $this->forge->createTable('custom_fields');
    }

    public function down()
    {
        $this->forge->dropTable('custom_fields');
        $this->forge->dropTable('custom_objects');
    }
}
