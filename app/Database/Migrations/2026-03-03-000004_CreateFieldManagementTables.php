<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFieldManagementTables extends Migration
{
    public function up()
    {
        // 1. project_punch_lists
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'item_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'assigned_to' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Open', 'Resolved', 'Closed'],
                'default'    => 'Open',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'fs_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'fs_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_punch_lists', true);


        // 2. project_site_diaries
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'report_date' => [
                'type' => 'DATE',
            ],
            'weather_conditions' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'temperature' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'work_performed' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'materials_received' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'safety_observations' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Approved'],
                'default'    => 'Draft',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'fs_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_site_diaries', true);


        // 3. project_site_diary_labor
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'diary_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'trade_or_company' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'worker_count' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'hours_worked' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('diary_id', 'project_site_diaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_site_diary_labor', true);
    }

    public function down()
    {
        $this->forge->dropTable('project_site_diary_labor', true);
        $this->forge->dropTable('project_site_diaries', true);
        $this->forge->dropTable('project_punch_lists', true);
    }
}
