<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'             => 'Starter Plan',
                'description'      => 'Perfect for small construction teams.',
                'price'            => 49.00,
                'currency'         => 'USD',
                'billing_interval' => 'monthly',
                'is_per_user'      => 0,
                'features'         => json_encode(['Basic CRM', 'Project Management', '5GB Storage']),
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Pro Plan (Per User)',
                'description'      => 'Scalable for growing companies.',
                'price'            => 15.00,
                'currency'         => 'USD',
                'billing_interval' => 'monthly',
                'is_per_user'      => 1,
                'features'         => json_encode(['Everything in Starter', 'Inventory & Assets', 'Payroll Engine', 'Stripe Integration']),
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Enterprise Plan',
                'description'      => 'Full ERP power for large organizations.',
                'price'            => 499.00,
                'currency'         => 'USD',
                'billing_interval' => 'monthly',
                'is_per_user'      => 0,
                'features'         => json_encode(['Everything in Pro', 'Custom Modules', 'Multi-Region Support', 'Priority Support']),
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('subscription_packages')->insertBatch($data);
    }
}
