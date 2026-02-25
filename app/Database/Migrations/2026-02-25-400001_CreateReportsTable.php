<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReportsTable extends Migration
{
    public function up(): void
    {
        // saved_reports – store report configurations / snapshots
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => 'null = global/cross-project'],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'type'        => ['type' => 'ENUM', 'constraint' => ['project_summary','cost','schedule','rfi','punch_list','site_diary','custom'], 'default' => 'project_summary'],
            'filters'     => ['type' => 'JSON', 'null' => true],
            'created_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'type']);
        $this->forge->createTable('saved_reports');

        // kpi_snapshots – daily/weekly aggregated KPI snapshots for trends
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'      => ['type' => 'INT', 'unsigned' => true],
            'snapshot_date'   => ['type' => 'DATE'],
            'tasks_total'     => ['type' => 'INT', 'default' => 0],
            'tasks_done'      => ['type' => 'INT', 'default' => 0],
            'rfis_open'       => ['type' => 'INT', 'default' => 0],
            'punch_open'      => ['type' => 'INT', 'default' => 0],
            'budget_total'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'cost_actual'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'snapshot_date']);
        $this->forge->createTable('kpi_snapshots');
    }

    public function down(): void
    {
        $this->forge->dropTable('kpi_snapshots', true);
        $this->forge->dropTable('saved_reports', true);
    }
}
