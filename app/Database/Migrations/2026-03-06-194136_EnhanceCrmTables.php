<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceCrmTables extends Migration
{
    public function up()
    {
        // 1. Clients
        $this->forge->addColumn('clients', [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
        ]);

        // 2. Leads
        $this->forge->addColumn('leads', [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
        ]);

        // 3. Tasks
        $this->forge->addColumn('tasks', [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('clients', ['tenant_id', 'branch_id']);
        $this->forge->dropColumn('leads', ['tenant_id', 'branch_id']);
        $this->forge->dropColumn('tasks', ['tenant_id', 'branch_id']);
    }
}
