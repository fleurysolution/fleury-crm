<?php

namespace App\Models;

class AssetModel extends ErpModel
{
    protected $table          = 'fs_assets';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'asset_tag',
        'name',
        'category',
        'status',
        'purchase_date',
        'purchase_price',
        'current_location_project_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
