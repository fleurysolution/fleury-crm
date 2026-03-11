<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceProjectsAndDailyLogs extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // 1. Enhance Projects Table
        if (!$db->fieldExists('tenant_id', 'projects')) {
            $this->forge->addColumn('projects', [
                'tenant_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id'
                ]
            ]);
        }
        if (!$db->fieldExists('branch_id', 'projects')) {
            $this->forge->addColumn('projects', [
                'branch_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'tenant_id'
                ]
            ]);
        }
        if (!$db->fieldExists('contract_type', 'projects')) {
            $this->forge->addColumn('projects', [
                'contract_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['lump_sum', 'cost_plus', 'unit_price', 't_and_m'],
                    'default'    => 'lump_sum',
                    'after'      => 'status'
                ],
                'project_stage' => [
                    'type'       => 'ENUM',
                    'constraint' => ['bidding', 'pre_construction', 'active', 'closeout', 'on_hold'],
                    'default'    => 'active',
                    'after'      => 'contract_type'
                ],
                'budget_baseline' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                    'after'      => 'project_stage'
                ],
                'actual_cost_to_date' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                    'after'      => 'budget_baseline'
                ]
            ]);
        }

        // 2. Enhance Project Site Diaries (Legacy compatibility)
        if ($db->tableExists('project_site_diaries')) {
            if (!$db->fieldExists('tenant_id', 'project_site_diaries')) {
                $this->forge->addColumn('project_site_diaries', [
                    'tenant_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'id'],
                    'branch_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
                ]);
            }
        }

        // 3. Create fs_daily_logs (New unified structure)
        if (!$db->tableExists('fs_daily_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'tenant_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'branch_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'project_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                ],
                'log_date' => [
                    'type' => 'DATE',
                ],
                'weather_conditions' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'temperature' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20',
                    'null'       => true,
                ],
                'site_conditions' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'work_performed' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_by' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                ],
                'approved_by' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['draft', 'submitted', 'approved'],
                    'default'    => 'draft',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['project_id', 'log_date']);
            $this->forge->createTable('fs_daily_logs');

            // 4. Create fs_daily_manpower
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'log_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                ],
                'trade' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'contractor_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'worker_count' => [
                    'type' => 'INT',
                    'default' => 0,
                ],
                'total_hours' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
                'work_description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('log_id', 'fs_daily_logs', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('fs_daily_manpower');

            // 5. Create fs_daily_equipment
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'log_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                ],
                'equipment_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'hours_used' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
                'operator_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('log_id', 'fs_daily_logs', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('fs_daily_equipment');
        }
    }

    public function down()
    {
        $this->forge->dropTable('fs_daily_equipment');
        $this->forge->dropTable('fs_daily_manpower');
        $this->forge->dropTable('fs_daily_logs');
        $this->forge->dropColumn('projects', ['tenant_id', 'branch_id', 'contract_type', 'project_stage', 'budget_baseline', 'actual_cost_to_date']);
    }
}
