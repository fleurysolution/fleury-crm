<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddErpFieldsToPunchList extends Migration
{
    public function up()
    {
        $fields = [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'project_id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
        ];
        $this->forge->addColumn('project_punch_lists', $fields);

        // Populate existing records with tenant/branch from projects
        $db = \Config\Database::connect();
        $db->query("
            UPDATE project_punch_lists
            JOIN projects ON projects.id = project_punch_lists.project_id
            SET project_punch_lists.tenant_id = projects.tenant_id, 
                project_punch_lists.branch_id = projects.branch_id
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('project_punch_lists', ['tenant_id', 'branch_id']);
    }
}
