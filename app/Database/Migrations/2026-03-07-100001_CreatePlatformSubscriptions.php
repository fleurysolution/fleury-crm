<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlatformSubscriptions extends Migration
{
    public function up()
    {
        // 1. Subscription Packages
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '3',
                'default'    => 'USD',
            ],
            'billing_interval' => [
                'type'       => 'ENUM',
                'constraint' => ['monthly', 'yearly'],
                'default'    => 'monthly',
            ],
            'stripe_price_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'features' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_per_user' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('subscription_packages');

        // 2. Tenant Subscriptions
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'package_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'trialing', 'cancelled', 'past_due', 'expired'],
                'default'    => 'trialing',
            ],
            'starts_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ends_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'current_period_start' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'current_period_end' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_billed_user_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'stripe_subscription_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('package_id', 'subscription_packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tenant_subscriptions');

        // 3. Update Tenants table
        $fields = [
            'stripe_customer_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'status',
            ],
            'package_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'stripe_customer_id',
            ],
        ];
        $this->forge->addColumn('tenants', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tenants', 'package_id');
        $this->forge->dropColumn('tenants', 'stripe_customer_id');
        $this->forge->dropTable('tenant_subscriptions');
        $this->forge->dropTable('subscription_packages');
    }
}
