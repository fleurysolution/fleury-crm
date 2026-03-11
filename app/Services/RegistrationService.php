<?php

namespace App\Services;

use App\Models\TenantModel;
use App\Models\FsUserModel;
use App\Models\TenantSubscriptionModel;
use App\Models\SubscriptionPackageModel;
use App\Models\RoleModel;
use App\Models\NotificationModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class RegistrationService
{
    protected $db;
    protected $tenantModel;
    protected $userModel;
    protected $subscriptionModel;
    protected $packageModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->tenantModel = new TenantModel();
        $this->userModel = new FsUserModel();
        $this->subscriptionModel = new TenantSubscriptionModel();
        $this->packageModel = new SubscriptionPackageModel();
    }

    /**
     * Register a new Tenant, Admin User, and Subscription.
     *
     * @param array $companyData
     * @param array $userData
     * @param int $packageId
     * @return int Tenant ID
     * @throws \Exception
     */
    public function registerTenant(array $companyData, array $userData, int $packageId)
    {
        $this->db->transBegin();

        try {
            // 1. Create Tenant
            $tenantId = $this->tenantModel->insert([
                'name'           => $companyData['name'],
                'industry'       => $companyData['industry'] ?? 'Construction',
                'employee_count' => $companyData['employee_count'] ?? 0,
                'country'        => $companyData['country'] ?? 'USA',
                'currency'       => $companyData['currency'] ?? 'USD',
                'timezone'       => $companyData['timezone'] ?? 'UTC',
                'status'         => 'active',
                'package_id'     => $packageId
            ]);

            if (!$tenantId) {
                throw new \Exception('Failed to create tenant.');
            }

            // 1.1 Create Main Branch
            $branchModel = new \App\Models\BranchModel();
            $branchId = $branchModel->insert([
                'tenant_id' => $tenantId,
                'name'      => 'Main Branch',
            ]);

            if (!$branchId) {
                throw new \Exception('Failed to create default branch.');
            }

            // 2. Resolve Role (Default to Admin for first user)
            // We assume role with slug 'admin' or ID 1 is the organizational admin
            $adminRole = $this->db->table('roles')->where('slug', 'admin')->get()->getRow();
            $roleId = $adminRole ? $adminRole->id : 1;

            // 3. Create Admin User
            $userId = $this->userModel->insert([
                'first_name' => $userData['first_name'],
                'last_name'  => $userData['last_name'],
                'email'      => $userData['email'],
                'password_hash' => password_hash($userData['password'], PASSWORD_DEFAULT),
                'role_id'    => $roleId,
                'tenant_id'  => $tenantId,
                'branch_id'  => $branchId,
                'geo_access_permission' => 'all', // Admin sees everything in their tenant
                'status'     => 'active',
                'is_admin'   => 1
            ]);

            if (!$userId) {
                throw new \Exception('Failed to create administrator account.');
            }

            // Assign role in relationship table if exists
            if ($this->db->tableExists('user_roles')) {
                $this->db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId
                ]);
            }

            // 4. Initialize Subscription
            $package = $this->packageModel->find($packageId);
            $interval = $package ? $package['billing_interval'] : 'monthly';
            $days = ($interval === 'yearly') ? 365 : 30;

            $this->subscriptionModel->insert([
                'tenant_id'  => $tenantId,
                'package_id' => $packageId,
                'status'     => 'trialing',
                'starts_at'  => date('Y-m-d H:i:s'),
                'ends_at'    => date('Y-m-d H:i:s', strtotime("+$days days")),
                'current_period_start' => date('Y-m-d H:i:s'),
                'current_period_end'   => date('Y-m-d H:i:s', strtotime("+$days days")),
            ]);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                throw new \Exception('Transaction failed.');
            }

            $this->db->transCommit();

            // 5. Create SaaS Client Mapping
            $this->createPlatformClient($companyData, $tenantId);

            // 6. Send Registration Notification
            NotificationModel::send(
                $userId,
                'registration_success',
                "Welcome to BPMS247!",
                [
                    'body' => "Your account and company '{$companyData['name']}' have been registered successfully.",
                    'url'  => 'dashboard'
                ]
            );

            return $tenantId;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Create a Client record in the system for the new tenant.
     * This allows the Super Admin to manage the construction company as a customer.
     */
    protected function createPlatformClient(array $companyData, int $tenantId)
    {
        // Platform Owner Tenant ID is typically 1
        $platformTenantId = 1; 

        $this->db->table('clients')->insert([
            'tenant_id'       => $platformTenantId,
            'company_name'    => $companyData['name'],
            'country'         => $companyData['country'],
            'currency'        => $companyData['currency'],
            'status'          => 'active',
            'type'            => 'organization',
            'labels'          => 'SaaS Subscriber',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        
        // Link the client back to the tenant if a field exists, 
        // otherwise it's just a logical mapping for now.
    }
}
