<?php

// Correct CI4 bootstrap for a standalone script
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('ENVIRONMENT', 'development');

require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();

require $paths->systemDirectory . '/bootstrap.php';

use App\Services\RegistrationService;

$service = new RegistrationService();

$uniqueId = time();
$userData = [
    'first_name' => 'Test',
    'last_name'  => 'Admin',
    'email'      => "test.admin.{$uniqueId}@example.com",
    'password'   => 'Password123!'
];

$companyData = [
    'name'           => "Test Complete Corp {$uniqueId}",
    'industry'       => 'Construction',
    'employee_count' => '50',
    'country'        => 'USA',
    'currency'       => 'USD',
    'timezone'       => 'UTC',
];

$packageId = 2; // Professional/Professional Plan

try {
    echo "Starting registration simulation for {$userData['email']}...\n";
    $tenantId = $service->registerTenant($companyData, $userData, $packageId);
    echo "SUCCESS! Tenant created with ID: $tenantId\n";
    
    // Output for next step
    echo "LOGIN_EMAIL: {$userData['email']}\n";
    echo "LOGIN_PASS: Password123!\n";

} catch (\Exception $e) {
    echo "PROVISIONING FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
