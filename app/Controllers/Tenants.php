<?php

namespace App\Controllers;

use App\Models\TenantModel;
use App\Models\TenantSubscriptionModel;

class Tenants extends BaseAppController
{
    protected $tenantModel;
    protected $subscriptionModel;

    public function __construct()
    {
        $this->tenantModel = new TenantModel();
        $this->subscriptionModel = new TenantSubscriptionModel();
    }

    public function index()
    {
        // Only Super Admins (tenant_id = 1 or null) can access this
        if ($this->loginUser->tenant_id !== null && $this->loginUser->tenant_id != 1) {
            return redirect()->to('dashboard')->with('error', 'Unauthorized access.');
        }

        $statusFilter = $this->request->getGet('status');
        
        // This is a bit complex because status is in the subscriptions table
        // For simplicity in this step, we'll fetch all and filter in PHP or use a join
        $db = \Config\Database::connect();
        $builder = $db->table('tenants t');
        $builder->select('t.*, ts.status as sub_status, ts.current_period_end as sub_end');
        $builder->join('tenant_subscriptions ts', 'ts.tenant_id = t.id AND ts.status = (SELECT status FROM tenant_subscriptions WHERE tenant_id = t.id ORDER BY id DESC LIMIT 1)', 'left');

        if ($statusFilter) {
            $builder->where('ts.status', $statusFilter);
        }

        $tenants = $builder->get()->getResultArray();
        
        // Map fields to match view expectations
        foreach ($tenants as &$t) {
            $t['subscription_status'] = $t['sub_status'] ?: 'none';
            $t['subscription_end'] = $t['sub_end'] ?: 'N/A';
        }

        $data = [
            'title' => 'Organization Management · BPMS247',
            'tenants' => $tenants,
            'current_status' => $statusFilter
        ];

        return $this->render('tenants/index', $data);
    }
}
