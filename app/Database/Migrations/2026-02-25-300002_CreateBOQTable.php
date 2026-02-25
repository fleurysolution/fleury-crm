<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBOQTable extends Migration
{
    public function up(): void
    {
        // BOQ line items
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'    => ['type' => 'INT', 'unsigned' => true],
            'contract_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cost_code_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'parent_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => 'for hierarchy / sections'],
            'item_code'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'description'   => ['type' => 'VARCHAR', 'constraint' => 500],
            'unit'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'quantity'      => ['type' => 'DECIMAL', 'constraint' => '12,4', 'default' => 0],
            'unit_rate'     => ['type' => 'DECIMAL', 'constraint' => '15,4', 'default' => 0],
            'total_amount'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0, 'comment' => 'qty * unit_rate'],
            'actual_qty'    => ['type' => 'DECIMAL', 'constraint' => '12,4', 'default' => 0, 'comment' => 'site-measured quantity'],
            'actual_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'is_section'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => 'header row only'],
            'sort_order'    => ['type' => 'INT', 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'sort_order']);
        $this->forge->createTable('boq_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('boq_items', true);
    }
}
