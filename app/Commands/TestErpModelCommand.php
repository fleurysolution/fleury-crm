<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ErpModel;

class TestErpDepartmentModel extends ErpModel {
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tenant_id', 'branch_id', 'name'];
}

class TestErpModelCommand extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:erpmodel';
    protected $description = 'Tests the branch_id enforcement in ErpModel';

    public function run(array $params)
    {
        CLI::write("Testing ErpModel isolation enforcement...", 'yellow');
        
        $model = new TestErpDepartmentModel();

        // 1. Test Insert without tenant_id (logged in simulation)
        session()->set(['is_logged_in' => true, 'tenant_id' => null]);
        try {
            CLI::write("Attempting to insert department without tenant_id (logged in but null)...");
            $model->insert(['name' => 'Isolation Test']);
            CLI::error("FAIL: Insert succeeded when it should have failed (mandatory tenant_id).");
        } catch (\RuntimeException $e) {
            CLI::write("SUCCESS: Caught expected exception -> " . $e->getMessage(), 'green');
        }

        // 2. Test Tenant Isolation
        CLI::write("Verifying tenant isolation...");
        session()->set(['is_logged_in' => true, 'tenant_id' => 10, 'user_roles' => ['admin']]);
        $id1 = $model->insert(['name' => 'Tenant 10 Dept', 'branch_id' => 1]);
        
        session()->set(['is_logged_in' => true, 'tenant_id' => 20, 'user_roles' => ['admin']]);
        $id2 = $model->insert(['name' => 'Tenant 20 Dept', 'branch_id' => 1]);

        // Attempting to find Tenant 10's dept while logged in as Tenant 20
        $found = $model->find($id1);
        if (!$found) {
            CLI::write("SUCCESS: Tenant 20 could NOT see Tenant 10's record.", 'green');
        } else {
            CLI::error("FAIL: Tenant 20 could see Tenant 10's record!");
        }

        // Cleanup
        $db = \Config\Database::connect();
        $db->table('departments')->whereIn('id', [$id1, $id2])->delete();
        CLI::write("Verification complete.", 'yellow');
    }
}
