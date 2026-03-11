<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTables extends Migration
{
    public function up()
    {
        // 1. fs_inventory_items
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'branch_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sku'             => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'category'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'unit_of_measure' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Each'],
            'reorder_level'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tenant_id');
        $this->forge->addKey('branch_id');
        $this->forge->createTable('fs_inventory_items', true);

        // 2. fs_inventory_locations
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'branch_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tenant_id');
        $this->forge->addKey('branch_id');
        $this->forge->createTable('fs_inventory_locations', true);

        // 3. fs_inventory_stocks
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'item_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'location_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'quantity'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('item_id');
        $this->forge->addKey('location_id');
        $this->forge->createTable('fs_inventory_stocks', true);

        // 4. fs_inventory_transactions
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'item_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'location_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'project_id_destination' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'quantity'               => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'transaction_type'       => ['type' => 'ENUM', 'constraint' => ['In', 'Out', 'Adjustment', 'Transfer']],
            'date'                   => ['type' => 'DATE'],
            'user_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('item_id');
        $this->forge->addKey('location_id');
        $this->forge->addKey('project_id_destination');
        $this->forge->createTable('fs_inventory_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_inventory_transactions', true);
        $this->forge->dropTable('fs_inventory_stocks', true);
        $this->forge->dropTable('fs_inventory_locations', true);
        $this->forge->dropTable('fs_inventory_items', true);
    }
}
