<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalFieldsToSiteDiary extends Migration
{
    public function up()
    {
        $this->forge->addColumn('project_site_diaries', [
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status'
            ],
            'approved_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'approved_at'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('project_site_diaries', ['approved_at', 'approved_by']);
    }
}
