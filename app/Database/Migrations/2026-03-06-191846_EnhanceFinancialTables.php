<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceFinancialTables extends Migration
{
    public function up()
    {
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
            ]
        ];

        $tables = [
            'project_invoices',
            'project_expenses',
            'project_estimates',
            'invoice_payments'
        ];

        $db = \Config\Database::connect();
        
        foreach ($tables as $table) {
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
    }

    public function down()
    {
        $tables = [
            'project_invoices',
            'project_expenses',
            'project_estimates',
            'invoice_payments'
        ];

        $db = \Config\Database::connect();

        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                $existing = $db->getFieldNames($table);
                $toDrop = [];
                if (in_array('branch_id', $existing)) {
                    $toDrop[] = 'branch_id';
                }
                if (in_array('tenant_id', $existing)) {
                    $toDrop[] = 'tenant_id';
                }
                if (!empty($toDrop)) {
                    $this->forge->dropColumn($table, $toDrop);
                }
            }
        }
    }
}
