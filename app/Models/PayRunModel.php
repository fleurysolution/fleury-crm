<?php

namespace App\Models;

class PayRunModel extends ErpModel
{
    protected $table          = 'fs_pay_runs';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'pay_period_start',
        'pay_period_end',
        'status',
        'approved_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
