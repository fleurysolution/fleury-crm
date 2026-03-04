<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table          = 'invoices';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields  = [
        'client_id',
        'project_id',
        'bill_date',
        'due_date',
        'type',
        'status',
        'note',
        'public_key',
        'tax_id',
        'tax_id2',
        'discount_amount',
        'discount_amount_type',
        'discount_type',
        'invoice_total',
        'payment_received',
        'recurring',
        'repeat_every',
        'repeat_type',
        'no_of_cycles',
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
