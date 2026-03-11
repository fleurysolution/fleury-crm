<?php

namespace App\Models;

class TaxProfileModel extends ErpModel
{
    protected $table          = 'fs_tax_profiles';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'name',
        'tax_rate',
        'region_code',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
