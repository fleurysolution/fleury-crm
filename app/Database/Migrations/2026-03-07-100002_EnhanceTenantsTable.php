<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTenantsTable extends Migration
{
    public function up()
    {
        $fields = [
            'industry' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'name',
            ],
            'employee_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'industry',
            ],
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'employee_count',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '3',
                'default'    => 'USD',
                'after'      => 'country',
            ],
            'timezone' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'UTC',
                'after'      => 'currency',
            ],
        ];
        $this->forge->addColumn('tenants', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tenants', ['industry', 'employee_count', 'country', 'currency', 'timezone']);
    }
}
