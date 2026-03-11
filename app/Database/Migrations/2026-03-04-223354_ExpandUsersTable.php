<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'tenant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'tenant_id'],
            'department_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'branch_id'],
            'reporting_manager_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'department_id'],
            'approval_authority_level' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'reporting_manager_id'],
            'geo_access_permission' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'approval_authority_level'],
            'payroll_profile_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'geo_access_permission'],
            'tax_profile_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'payroll_profile_id'],
            'employment_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'tax_profile_id'],
        ];
        
        $this->forge->addColumn('fs_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('fs_users', [
            'tenant_id', 'branch_id', 'department_id', 'reporting_manager_id', 
            'approval_authority_level', 'geo_access_permission', 
            'payroll_profile_id', 'tax_profile_id', 'employment_type'
        ]);
    }
}
