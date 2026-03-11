<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceApprovalWorkflows extends Migration
{
    public function up()
    {
        $fields = [
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'module_key',
            ],
            'min_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'description',
            ],
            'max_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'min_amount',
            ]
        ];

        $this->forge->addColumn('fs_as_approval_workflows', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('fs_as_approval_workflows', ['branch_id', 'min_amount', 'max_amount']);
    }
}
