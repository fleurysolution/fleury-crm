<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConstructionSettingsTable extends Migration
{
    public function up(): void
    {
        // construction_settings – project defaults for the construction module
        // Stores k/v pairs for construction-specific configuration
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'group'      => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'general'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('key');
        $this->forge->createTable('construction_settings');

        // Seed default settings
        $db = \Config\Database::connect();
        $defaults = [
            // Project defaults
            ['key' => 'default_currency',       'value' => 'USD',      'group' => 'project'],
            ['key' => 'default_currency_symbol','value' => '$',        'group' => 'project'],
            ['key' => 'default_retention_pct',  'value' => '10',        'group' => 'project'],
            ['key' => 'boq_unit_list',          'value' => 'm2,m3,m,no,kg,tonnes,ls,hr', 'group' => 'boq'],
            ['key' => 'boq_section_prefix',     'value' => '1',         'group' => 'boq'],
            ['key' => 'ipc_prefix',             'value' => 'IPC-',      'group' => 'finance'],
            ['key' => 'contract_number_prefix', 'value' => 'CON-',      'group' => 'contracts'],
            ['key' => 'task_default_status',    'value' => 'todo',      'group' => 'project'],
            ['key' => 'gantt_working_days',     'value' => '5',         'group' => 'project'],
            ['key' => 'company_name',           'value' => 'My Company','group' => 'company'],
            ['key' => 'company_address',        'value' => '',          'group' => 'company'],
            ['key' => 'company_phone',          'value' => '',          'group' => 'company'],
            ['key' => 'company_email',          'value' => '',          'group' => 'company'],
            ['key' => 'company_logo',           'value' => '',          'group' => 'company'],
        ];
        $db->table('construction_settings')->insertBatch(array_map(fn($r) => array_merge($r, ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]), $defaults));
    }

    public function down(): void
    {
        $this->forge->dropTable('construction_settings', true);
    }
}
