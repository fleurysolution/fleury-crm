<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantSubscriptionModel extends Model
{
    protected $table          = 'tenant_subscriptions';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = [
        'tenant_id',
        'package_id',
        'status',
        'starts_at',
        'ends_at',
        'current_period_start',
        'current_period_end',
        'last_billed_user_count',
        'stripe_subscription_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function get_current_subscription($tenant_id)
    {
        return $this->where('tenant_id', $tenant_id)
                    ->whereIn('status', ['active', 'trialing'])
                    ->orderBy('id', 'DESC')
                    ->first();
    }
}
