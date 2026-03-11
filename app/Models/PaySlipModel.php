<?php

namespace App\Models;

class PaySlipModel extends ErpModel
{
    protected $table          = 'fs_pay_slips';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'pay_run_id',
        'user_id',
        'gross_pay',
        'net_pay',
        'taxes_withheld',
        'deductions',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // No direct explicit tenant_id checks strictly enforced here because it relies on the pay_run_id linkage
    // and is automatically queried via the parent PayRun contexts.
    protected $enforceBranchLinkage = false; 
}
