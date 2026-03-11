<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHourlyRateToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'hourly_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'employment_type'
            ],
        ];
        $this->forge->addColumn('fs_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('fs_users', 'hourly_rate');
    }
}
