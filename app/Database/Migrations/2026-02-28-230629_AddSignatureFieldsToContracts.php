<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSignatureFieldsToContracts extends Migration
{
    public function up()
    {
        $fields = [
            'client_signed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'client_ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'client_signature_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'contractor_signed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'contractor_ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'contractor_signature_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('project_contracts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('project_contracts', [
            'client_signed_at', 
            'client_ip_address', 
            'client_signature_data', 
            'contractor_signed_at', 
            'contractor_ip_address', 
            'contractor_signature_data'
        ]);
    }
}
