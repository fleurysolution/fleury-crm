<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSOVTables extends Migration
{
    public function up()
    {
        // 1. project_sov_items table (Master Contract Breakdown)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'item_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'scheduled_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addKey('project_id');
        $this->forge->createTable('project_sov_items', true);

        // 2. project_pay_apps table (Master Invoices)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'application_no' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'period_to' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Approved', 'Paid', 'Rejected'],
                'default'    => 'Draft',
            ],
            'retainage_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 10.00, // 10% standard retention
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addKey('project_id');
        $this->forge->createTable('project_pay_apps', true);

        // 3. project_pay_app_items table (Line Item Progress)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pay_app_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'sov_item_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'work_completed_this_period' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'materials_presently_stored' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pay_app_id');
        $this->forge->createTable('project_pay_app_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('project_pay_app_items', true);
        $this->forge->dropTable('project_pay_apps', true);
        $this->forge->dropTable('project_sov_items', true);
    }
}
