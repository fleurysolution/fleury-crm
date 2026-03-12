<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSiteDiaryForProduction extends Migration
{
    public function up()
    {
        // 4. Site Diary Items - Production tracking
        $this->forge->addColumn('site_diary_items', [
            'boq_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'task_id'
            ],
            'quantity_done' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
                'default' => 0.00,
                'after' => 'boq_item_id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('site_diary_items', ['boq_item_id', 'quantity_done']);
    }
}
