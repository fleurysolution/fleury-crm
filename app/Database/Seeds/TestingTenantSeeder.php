<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Services\RegistrationService;

class TestingTenantSeeder extends Seeder
{
    public function run()
    {
        $registrationService = new RegistrationService();

        $testCompanies = [
            [
                'company' => [
                    'name'           => 'ABC Infrastructure Pvt Ltd',
                    'industry'       => 'Construction',
                    'employee_count' => 25,
                    'country'        => 'India',
                    'currency'       => 'INR',
                    'timezone'       => 'Asia/Kolkata',
                ],
                'user' => [
                    'first_name' => 'Amit',
                    'last_name'  => 'Sharma',
                    'email'      => 'admin@abcinfra.com',
                    'password'   => 'password123',
                ],
                'package_id' => 2, // Pro Plan (Per User)
            ],
            [
                'company' => [
                    'name'           => 'BuildRight Solutions',
                    'industry'       => 'Real Estate',
                    'employee_count' => 10,
                    'country'        => 'USA',
                    'currency'       => 'USD',
                    'timezone'       => 'America/New_York',
                ],
                'user' => [
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'email'      => 'john@buildright.com',
                    'password'   => 'password123',
                ],
                'package_id' => 1, // Starter Plan
            ],
            [
                'company' => [
                    'name'           => 'Skyline Mega Projects',
                    'industry'       => 'Infrastructure',
                    'employee_count' => 100,
                    'country'        => 'UK',
                    'currency'       => 'GBP',
                    'timezone'       => 'Europe/London',
                ],
                'user' => [
                    'first_name' => 'Jane',
                    'last_name'  => 'Smith',
                    'email'      => 'jane@skyline.co.uk',
                    'password'   => 'password123',
                ],
                'package_id' => 3, // Enterprise Plan
            ],
        ];

        foreach ($testCompanies as $data) {
            try {
                $registrationService->registerTenant($data['company'], $data['user'], $data['package_id']);
            } catch (\Exception $e) {
                echo "Failed to seed " . $data['company']['name'] . ": " . $e->getMessage() . "\n";
            }
        }
    }
}
