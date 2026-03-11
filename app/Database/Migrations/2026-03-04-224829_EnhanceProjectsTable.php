<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceProjectsTable extends Migration
{
    public function up()
    {
        $fields = [
            'tenant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'tenant_id',
            ],
            'contract_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'budget',
            ],
            'versioned_budget_baseline' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'contract_type',
            ],
            'project_stage' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'status',
            ],
        ];

        $this->forge->addColumn('projects', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('projects', ['tenant_id', 'branch_id', 'contract_type', 'versioned_budget_baseline', 'project_stage']);
    }
}
