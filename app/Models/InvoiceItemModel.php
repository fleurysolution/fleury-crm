<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItemModel extends Model
{
    protected $table          = 'invoice_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // Usually items are hard deleted if invoice is deleted, or handled by cascade

    protected $allowedFields  = [
        'invoice_id',
        'title',
        'description',
        'quantity',
        'rate',
        'total',
        'sort'
    ];
}
