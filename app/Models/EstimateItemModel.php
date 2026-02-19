<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateItemModel extends Model
{
    protected $table          = 'estimate_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'estimate_id',
        'title',
        'description',
        'quantity',
        'rate',
        'total',
        'sort'
    ];
}
