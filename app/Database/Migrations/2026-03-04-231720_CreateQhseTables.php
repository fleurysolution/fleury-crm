<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQhseTables extends Migration
{
    public function up()
    {
        // 1. fs_inspections
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'draft'], // draft, scheduled, in_progress, completed, failed
            'inspector_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'inspection_date' => ['type' => 'DATE', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_inspections', true);

        // 2. fs_inspection_items
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'inspection_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'description' => ['type' => 'TEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'pending'], // pending, pass, fail, not_applicable
            'remarks' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('inspection_id', 'fs_inspections', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_inspection_items', true);

        // 3. fs_safety_incidents
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'incident_date' => ['type' => 'DATETIME'],
            'type' => ['type' => 'VARCHAR', 'constraint' => 100], // injury, near_miss, property_damage, environmental
            'severity' => ['type' => 'VARCHAR', 'constraint' => 50], // low, medium, high, critical
            'description' => ['type' => 'TEXT'],
            'reported_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'open'], // open, under_investigation, resolved, closed
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_safety_incidents', true);

        // 4. Refactoring punch_list_items to include tenant_id and branch_id
        $fields = [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'project_id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
        ];
        $this->forge->addColumn('punch_list_items', $fields);
        
        // Populate existing punch list items with tenant/branch from projects
        $db = \Config\Database::connect();
        $db->query("
            UPDATE punch_list_items pli
            JOIN projects p ON p.id = pli.project_id
            SET pli.tenant_id = p.tenant_id, pli.branch_id = p.branch_id
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('punch_list_items', ['tenant_id', 'branch_id']);
        $this->forge->dropTable('fs_safety_incidents', true);
        $this->forge->dropTable('fs_inspection_items', true);
        $this->forge->dropTable('fs_inspections', true);
    }
}
