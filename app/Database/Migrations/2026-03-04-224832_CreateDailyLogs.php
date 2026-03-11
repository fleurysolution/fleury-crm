<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyLogs extends Migration
{
    public function up()
    {
        // fs_daily_logs
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'project_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'date' => ['type' => 'DATE'],
            'weather_conditions' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'temperature' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'site_conditions' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'draft'], // draft, submitted, approved
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_daily_logs', true);

        // fs_daily_manpower
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'log_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'trade_or_contractor' => ['type' => 'VARCHAR', 'constraint' => 255],
            'worker_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'hours' => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0.00],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('log_id', 'fs_daily_logs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_daily_manpower', true);

        // fs_daily_equipment
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'log_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'equipment_type' => ['type' => 'VARCHAR', 'constraint' => 255],
            'hours_used' => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0.00],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // e.g. operational, idle, broken
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('log_id', 'fs_daily_logs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fs_daily_equipment', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_daily_equipment', true);
        $this->forge->dropTable('fs_daily_manpower', true);
        $this->forge->dropTable('fs_daily_logs', true);
    }
}
