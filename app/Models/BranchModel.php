<?php

namespace App\Models;

class BranchModel extends ErpModel
{
    protected $table          = 'branches';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    // Branches are not linked to other branches
    protected $enforceBranchLinkage = false;

    protected $allowedFields = [
        'tenant_id',
        'division_id',
        'region_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
