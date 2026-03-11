<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactInfoToBranches extends Migration
{
    public function up()
    {
        $fields = [
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'name',
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'address',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'phone',
            ],
        ];
        $this->forge->addColumn('branches', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('branches', ['address', 'phone', 'email']);
    }
}
