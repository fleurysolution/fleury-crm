<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRemarksToBids extends Migration
{
    public function up()
    {
        $this->forge->addColumn('project_bids', [
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after'=> 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('project_bids', 'remarks');
    }
}
