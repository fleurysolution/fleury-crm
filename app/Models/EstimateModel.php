<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateModel extends ErpModel
{
    protected $table          = 'estimates';
    protected $primaryKey     = 'id';
    protected $allowedFields  = [
        'tenant_id',
        'branch_id',
        'client_id',
        'lead_id',
        'estimate_date',
        'valid_until',
        'currency',
        'currency_symbol',
        'status',
        'note',
        'public_key',
        'tax_id',
        'tax_id2',
        'discount_amount',
        'discount_amount_type',
        'discount_type',
        'created_by'
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
