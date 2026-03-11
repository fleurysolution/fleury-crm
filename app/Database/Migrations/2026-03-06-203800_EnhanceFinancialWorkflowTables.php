<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceFinancialWorkflowTables extends Migration
{
    public function up()
    {
        $fields = [
            'tenant_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ],
            'branch_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tenant_id'
            ],
        ];

        $this->forge->addColumn('project_pay_apps', $fields);
        $this->forge->addColumn('estimates', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('project_pay_apps', ['tenant_id', 'branch_id']);
        $this->forge->dropColumn('estimates', ['tenant_id', 'branch_id']);
    }
}
