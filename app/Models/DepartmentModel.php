<?php

namespace App\Models;

class DepartmentModel extends ErpModel
{
    protected $table          = 'departments';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    // Departments ARE linked to a specific branch
    protected $enforceBranchLinkage = true;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
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
