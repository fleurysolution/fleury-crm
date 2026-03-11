<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceApprovalSteps extends Migration
{
    public function up()
    {
        $fields = [
            'escalation_role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'sla_hours',
            ],
            'escalation_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'escalation_role_id',
            ],
        ];

        $this->forge->addColumn('fs_as_approval_workflow_steps', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('fs_as_approval_workflow_steps', ['escalation_role_id', 'escalation_user_id']);
    }
}
