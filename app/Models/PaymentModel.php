<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends ErpModel
{
    protected $table          = 'invoice_payments';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields  = [
        'tenant_id',
        'branch_id',
        'invoice_id',
        'payment_method_id',
        'payment_date',
        'amount',
        'transaction_id',
        'note',
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
