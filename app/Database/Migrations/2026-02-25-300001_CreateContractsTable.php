<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContractsTable extends Migration
{
    public function up(): void
    {
        // project_contracts – construction contract register
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'      => ['type' => 'INT', 'unsigned' => true],
            'contract_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'title'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'type'            => ['type' => 'ENUM', 'constraint' => ['main','subcontract','supply','consultant','other'], 'default' => 'main'],
            'contractor_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'client_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['draft','active','on_hold','completed','terminated'], 'default' => 'draft'],
            'scope'           => ['type' => 'TEXT', 'null' => true],
            'value'           => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'currency'        => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'USD'],
            'retention_pct'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 10.00],
            'start_date'      => ['type' => 'DATE', 'null' => true],
            'end_date'        => ['type' => 'DATE', 'null' => true],
            'signed_by'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'signed_at'       => ['type' => 'DATETIME', 'null' => true],
            'filepath'        => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_by'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('project_contracts');

        // project_contract_amendments – variation orders
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'contract_id'  => ['type' => 'INT', 'unsigned' => true],
            'amendment_no' => ['type' => 'TINYINT', 'default' => 1],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'value_change' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'time_change'  => ['type' => 'INT', 'default' => 0],
            'status'       => ['type' => 'ENUM', 'constraint' => ['pending','approved','rejected'], 'default' => 'pending'],
            'approved_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['contract_id']);
        $this->forge->createTable('project_contract_amendments');
    }

    public function down(): void
    {
        $this->forge->dropTable('project_contract_amendments', true);
        $this->forge->dropTable('project_contracts', true);
    }
}
