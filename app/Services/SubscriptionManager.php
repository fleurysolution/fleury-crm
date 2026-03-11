<?php

namespace App\Services;

use App\Models\TenantSubscriptionModel;

class SubscriptionManager
{
    protected $subscriptionModel;

    public function __construct()
    {
        $this->subscriptionModel = new TenantSubscriptionModel();
    }

    /**
     * Check if a tenant has a valid active subscription.
     */
    public function isSubscriptionActive($tenantId)
    {
        if (!$tenantId) return false;

        $sub = $this->subscriptionModel->get_current_subscription($tenantId);
        
        if (!$sub) return false;

        // Check if current date is within the period
        $now = date('Y-m-d H:i:s');
        if ($sub['current_period_end'] < $now) {
            return false;
        }

        return in_array($sub['status'], ['active', 'trialing']);
    }

    /**
     * Get the subscription status label.
     */
    public function getStatus($tenantId)
    {
        $sub = $this->subscriptionModel->get_current_subscription($tenantId);
        return $sub ? $sub['status'] : 'none';
    }
}
