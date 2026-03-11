<?php

namespace App\Models;

class InventoryStockModel extends ErpModel
{
    protected $table          = 'fs_inventory_stocks';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'item_id',
        'location_id',
        'quantity',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = ''; // Prevent auto-set for created_at
    protected $updatedField  = 'updated_at';

    // No direct explicit tenant_id checks strictly enforced here because it relies on the item_id/location_id
    protected $enforceBranchLinkage = false; 
}
