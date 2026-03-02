<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSignaturesToApprovals extends Migration
{
    public function up()
    {
        $fields = [
            'signed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'signature_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'signature_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('fs_as_approval_request_steps', $fields);
        $this->forge->addColumn('project_contract_amendments', $fields);
        $this->forge->addColumn('submittal_revisions', $fields);
    }

    public function down()
    {
        $columns = ['signed_at', 'signature_ip', 'signature_data'];
        
        $this->forge->dropColumn('fs_as_approval_request_steps', $columns);
        $this->forge->dropColumn('project_contract_amendments', $columns);
        $this->forge->dropColumn('submittal_revisions', $columns);
    }
}
