<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddErpFieldsToProcurement extends Migration
{
    public function up()
    {
        $fields = [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'project_id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
        ];
        
        $this->forge->addColumn('project_purchase_orders', $fields);
        $this->forge->addColumn('project_bids', $fields);

        // Populate existing records with tenant/branch from projects
        $db = \Config\Database::connect();
        
        $db->query("
            UPDATE project_purchase_orders
            JOIN projects ON projects.id = project_purchase_orders.project_id
            SET project_purchase_orders.tenant_id = projects.tenant_id, 
                project_purchase_orders.branch_id = projects.branch_id
        ");

        $db->query("
            UPDATE project_bids
            JOIN projects ON projects.id = project_bids.project_id
            SET project_bids.tenant_id = projects.tenant_id, 
                project_bids.branch_id = projects.branch_id
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('project_purchase_orders', ['tenant_id', 'branch_id']);
        $this->forge->dropColumn('project_bids', ['tenant_id', 'branch_id']);
    }
}
