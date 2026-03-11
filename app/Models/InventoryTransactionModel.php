<?php

namespace App\Models;

class InventoryTransactionModel extends ErpModel
{
    protected $table          = 'fs_inventory_transactions';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // Immutable log

    protected $allowedFields = [
        'item_id',
        'location_id',
        'project_id_destination',
        'quantity',
        'transaction_type',
        'date',
        'user_id',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Immutable log

    // No direct explicit tenant_id checks strictly enforced here because it relies on the item_id
    protected $enforceBranchLinkage = false; 
}
