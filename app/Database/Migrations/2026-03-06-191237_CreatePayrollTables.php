<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayrollTables extends Migration
{
    public function up()
    {
        // 1. Payroll Profiles (Branch-level settings)
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'branch_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'pay_period'       => ['type' => 'ENUM', 'constraint' => ['Weekly', 'Bi-Weekly', 'Semi-Monthly', 'Monthly'], 'default' => 'Bi-Weekly'],
            'overtime_rule_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['tenant_id', 'branch_id']);
        $this->forge->createTable('fs_payroll_profiles', true);

        // 2. Tax Profiles (Local tax rates, branch-level configs)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'branch_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'tax_rate'   => ['type' => 'DECIMAL', 'constraint' => '5,2'], // e.g., 15.50
            'region_code'=> ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fs_tax_profiles', true);

        // 3. Pay Runs (A single batch processing of payroll)
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'branch_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'pay_period_start' => ['type' => 'DATE'],
            'pay_period_end'   => ['type' => 'DATE'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['Draft', 'Pending Approval', 'Approved', 'Paid'], 'default' => 'Draft'],
            'approved_by'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fs_pay_runs', true);

        // 4. Pay Slips (Individual paycheck records for a Pay Run)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pay_run_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'gross_pay'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'net_pay'        => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'taxes_withheld' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'deductions'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pay_run_id');
        $this->forge->createTable('fs_pay_slips', true);
    }

    public function down()
    {
        $this->forge->dropTable('fs_pay_slips', true);
        $this->forge->dropTable('fs_pay_runs', true);
        $this->forge->dropTable('fs_tax_profiles', true);
        $this->forge->dropTable('fs_payroll_profiles', true);
    }
}
