<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionPackageModel extends Model
{
    protected $table          = 'subscription_packages';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = [
        'name',
        'description',
        'price',
        'currency',
        'billing_interval',
        'stripe_price_id',
        'features',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActivePackages()
    {
        return $this->where('status', 'active')->findAll();
    }
}
