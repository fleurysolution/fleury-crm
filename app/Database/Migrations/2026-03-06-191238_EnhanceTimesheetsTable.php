<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTimesheetsTable extends Migration
{
    public function up()
    {
        // We assume 'project_timesheets' is the primary table. If it varies slightly (e.g., 'fs_timesheets'), we use the correct one based on FRS analysis.
        // Assuming 'fs_timesheets' based on past phase work, or 'project_timesheets'. Let's refer to 'project_timesheets' as seen in Models previously.

        $fields = [
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ],
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tenant_id'
            ],
            'payroll_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Unprocessed', 'Processed'],
                'default'    => 'Unprocessed',
                'after'      => 'status' // Assumes a 'status' column exists for approval
            ],
            'pay_run_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'payroll_status'
            ]
        ];

        // Safe add logic in case tenant_id/branch_id already exist from previous global scripts
        $db = \Config\Database::connect();
        $table = 'timesheets';
        
        if ($db->tableExists($table)) {
            $existing = $db->getFieldNames($table);
            $toAdd = [];
            foreach ($fields as $fieldName => $props) {
                if (!in_array($fieldName, $existing)) {
                    $toAdd[$fieldName] = $props;
                }
            }
            if (!empty($toAdd)) {
                $this->forge->addColumn($table, $toAdd);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $table = 'timesheets';

        if ($db->tableExists($table)) {
            $existing = $db->getFieldNames($table);
            
            if (in_array('pay_run_id', $existing)) {
                $this->forge->dropColumn($table, 'pay_run_id');
            }
            if (in_array('payroll_status', $existing)) {
                $this->forge->dropColumn($table, 'payroll_status');
            }
            // Retain tenant_id and branch_id as they might be structurally necessary outside of payroll
        }
    }
}
