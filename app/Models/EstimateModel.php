<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table          = 'estimates';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields  = [
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
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
