<?php

namespace App\Models;

class InventoryItemModel extends ErpModel
{
    protected $table          = 'fs_inventory_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'sku',
        'name',
        'description',
        'category',
        'unit_of_measure',
        'reorder_level',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
