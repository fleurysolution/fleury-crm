<?php

// Mocking the environment for CI4
define('FCPATH', __DIR__ . '/public/');
require_once 'vendor/autoload.php';
require_once 'app/Config/Constants.php';

$helpers = ['url', 'form'];
foreach ($helpers as $helper) {
    require_once "system/Helpers/{$helper}_helper.php";
}

// Bootstrap the framework
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Services\RegistrationService;

$service = new RegistrationService();

$userData = [
    'first_name' => 'John',
    'last_name'  => 'Doe',
    'email'      => 'john.doe.test.' . time() . '@example.com',
    'password'   => 'Secret123!'
];

$companyData = [
    'name'           => 'Doe Construction Ltd ' . time(),
    'industry'       => 'Residential',
    'employee_count' => '25',
    'country'        => 'Canada',
    'currency'       => 'CAD',
    'timezone'       => 'America/Toronto',
];

$packageId = 1; // Basic/Starter

try {
    echo "Starting registration simulation...\n";
    $tenantId = $service->registerTenant($companyData, $userData, $packageId);
    echo "SUCCESS! Tenant created with ID: $tenantId\n";
    
    // Verify records
    $db = \Config\Database::connect();
    $tenant = $db->table('tenants')->where('id', $tenantId)->get()->getRow();
    $user = $db->table('fs_users')->where('tenant_id', $tenantId)->get()->getRow();
    $sub = $db->table('tenant_subscriptions')->where('tenant_id', $tenantId)->get()->getRow();
    
    if ($tenant && $user && $sub) {
        echo "Database objects verified:\n";
        echo "- Tenant: " . $tenant->name . "\n";
        echo "- User: " . $user->email . "\n";
        echo "- Subscription Status: " . $sub->status . "\n";
    } else {
        echo "ERROR: Missing database records!\n";
    }

} catch (\Exception $e) {
    echo "PROVISIONING FAILED: " . $e->getMessage() . "\n";
}
