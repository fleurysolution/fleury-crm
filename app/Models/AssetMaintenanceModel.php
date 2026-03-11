<?php

namespace App\Models;

class AssetMaintenanceModel extends ErpModel
{
    protected $table          = 'fs_asset_maintenance';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'asset_id',
        'maintenance_date',
        'description',
        'cost',
        'performed_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // No direct explicit tenant_id checks strictly enforced here because it relies on the asset_id.
    protected $enforceBranchLinkage = false; 
}
