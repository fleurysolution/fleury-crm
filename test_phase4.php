<?php

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Simulate a user login session with a specific tenant and branch
$session = session();
$session->set([
    'user_id' => 1,
    'tenant_id' => 1,
    'branch_id' => 10,
    'is_logged_in' => true
]);

echo "--- Testing Phase 4: ERP Projects and Daily Logs ---\n";

$projects = new \App\Models\ProjectModel();
$dailyLogs = new \App\Models\DailyLogModel();
$manpower = new \App\Models\DailyManpowerModel();

// 1. Create a Project
try {
    $projectId = $projects->insert([
        'title' => 'Test ERP Project - Branch 10',
        'contract_type' => 'lump_sum',
        'versioned_budget_baseline' => 50000.00,
        'project_stage' => 'active'
    ]);
    
    $p = $projects->find($projectId);
    echo "Successfully created Project ID {$projectId}\n";
    echo "  -> Linked Tenant ID: " . ($p['tenant_id'] ?? 'NULL') . "\n";
    echo "  -> Linked Branch ID: " . ($p['branch_id'] ?? 'NULL') . "\n";
    
    if ($p['branch_id'] != 10) {
        echo "FAILED: Expected branch_id to be 10 (auto-injected by ErpModel).\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "Error inserting project: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Create a Daily Log
try {
    $logId = $dailyLogs->insert([
        'project_id' => $projectId,
        'date' => date('Y-m-d'),
        'weather_conditions' => 'Sunny, 75F',
        'site_conditions' => 'Good',
        'notes' => 'Site grading commenced.'
    ]);
    
    $l = $dailyLogs->find($logId);
    echo "Successfully created Daily Log ID {$logId}\n";
    echo "  -> Linked Branch ID: " . ($l['branch_id'] ?? 'NULL') . "\n";

    if ($l['branch_id'] != 10) {
        echo "FAILED: Expected branch_id to be 10 on daily log.\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "Error inserting daily log: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Add Manpower
try {
    $mpId = $manpower->insert([
        'log_id' => $logId,
        'trade_or_contractor' => 'Excavation Corp',
        'worker_count' => 4,
        'hours' => 32
    ]);
    echo "Successfully added Manpower Entry ID {$mpId}\n";
} catch (\Exception $e) {
    echo "Error inserting manpower: " . $e->getMessage() . "\n";
    exit(1);
}

echo "SUCCESS: Phase 4 data linkage and schema verification passed.\n";

// Cleanup test data
$manpower->delete($mpId);
$dailyLogs->delete($logId, true);
$projects->delete($projectId, true);
echo "Cleanup completed.\n";

exit(0);
