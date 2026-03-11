<?php

namespace App\Models;

class AssetAssignmentModel extends ErpModel
{
    protected $table          = 'fs_asset_assignments';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // Assignment log is immutable

    protected $allowedFields = [
        'asset_id',
        'project_id',
        'assigned_to_user_id',
        'assigned_date',
        'return_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // No direct explicit tenant_id checks strictly enforced here because it relies on the asset_id 
    // being loaded and manipulated securely by AssetModel, but ErpModel provides standard protection.
    // However since fs_asset_assignments doesn't have tenant_id/branch_id columns itself, 
    // it will inherit ErpBase methods but bypass branch enforce IF fields don't exist.
    protected $enforceBranchLinkage = false; 
}
