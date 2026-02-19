<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table = 'subscriptions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'title', 'next_billing_date', 'status'];
    protected $useTimestamps = true;
}
