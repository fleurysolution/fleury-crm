<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetTables extends Migration
{
    public function up()
    {
        // 1. fs_assets
        $this->forge->addField([
            'id'                          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'branch_id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'asset_tag'                   => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'name'                        => ['type' => 'VARCHAR', 'constraint' => 255],
            'category'                    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'                      => ['type' => 'ENUM', 'constraint' => ['Active', 'In Use', 'Maintenance', 'Retired'], 'default' => 'Active'],
            'purchase_date'               => ['type' => 'DATE', 'null' => true],
            'purchase_price'              => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'current_location_project_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'                  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'                  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tenant_id');
        $this->forge->addKey('branch_id');
        $this->forge->createTable('fs_assets', true);

        // 2. fs_asset_assignments
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'asset_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'project_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'assigned_to_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'assigned_date'       => ['type' => 'DATE'],
            'return_date'         => ['type' => 'DATE', 'null' => true],
            'status'              => ['type' => 'ENUM', 'constraint' => ['Assigned', 'Returned'], 'default' => 'Assigned'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('asset_id');
        $this->forge->addKey('project_id');
        $this->forge->createTable('fs_asset_assignments', true);

        // 3. fs_asset_maintenance
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'asset_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'maintenance_date' => ['type' => 'DATE'],
            'description'      => ['type' => 'TEXT'],
            'cost'             => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'performed_by'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('asset_id');
        $this->forge->createTable('fs_asset_maintenance', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_asset_maintenance', true);
        $this->forge->dropTable('fs_asset_assignments', true);
        $this->forge->dropTable('fs_assets', true);
    }
}
