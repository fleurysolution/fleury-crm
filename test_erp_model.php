<?php

// A simple script to test ErpModel logic
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(FCPATH);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

use App\Models\ErpModel;

class TestErpModel extends ErpModel {
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tenant_id', 'branch_id', 'name'];
}

$model = new TestErpModel();

try {
    echo "Attempting to insert department without branch_id...\n";
    $model->insert([
        'tenant_id' => 1,
        'name' => 'HR Department'
    ]);
    echo "FAIL: Insert succeeded when it should have failed.\n";
} catch (\RuntimeException $e) {
    echo "SUCCESS: Caught exception -> " . $e->getMessage() . "\n";
}

try {
    echo "\nAttempting to insert department WITH branch_id...\n";
    $id = $model->insert([
        'tenant_id' => 1,
        'branch_id' => 1,
        'name' => 'HR Department'
    ]);
    if ($id) {
        echo "SUCCESS: Insert with branch_id succeeded. ID: $id\n";
    } else {
        echo "FAIL: Insert returned false. Errors: " . print_r($model->errors(), true) . "\n";
    }
} catch (\Exception $e) {
    echo "FAIL: Unexpected exception -> " . $e->getMessage() . "\n";
}
