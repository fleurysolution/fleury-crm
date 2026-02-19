<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeadSupport extends Migration
{
    public function up()
    {
        // Add lead_id to Notes
        $this->forge->addColumn('notes', [
            'lead_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'client_id']
        ]);
        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE'); // Assuming 'leads' table exists

        // Add lead_id to General Files
         $this->forge->addColumn('general_files', [
            'lead_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'client_id']
        ]);
        // Foreign key might fail if leads table doesn't exist or is MyISAM. Assuming InnoDB and exists.
        // $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');

        // Add lead_id to Estimates
         $this->forge->addColumn('estimates', [
            'lead_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'client_id']
        ]);
        
        // Add lead_id to Events
         $this->forge->addColumn('events', [
            'lead_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'client_id']
        ]);

        // Create Tasks Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'lead_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'project_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'assigned_to' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['to_do', 'in_progress', 'done'], 'default' => 'to_do'],
            'start_date' => ['type' => 'DATE', 'null' => true],
            'deadline' => ['type' => 'DATE', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tasks', true);
    }

    public function down()
    {
        $this->forge->dropColumn('notes', 'lead_id');
        $this->forge->dropColumn('general_files', 'lead_id');
        $this->forge->dropColumn('estimates', 'lead_id');
        $this->forge->dropColumn('events', 'lead_id');
        $this->forge->dropTable('tasks', true);
    }
}
