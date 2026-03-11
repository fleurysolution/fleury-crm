<?php

namespace App\Models;

use CodeIgniter\Model;

class FsApprovalWorkflowModel extends Model
{
    protected $table          = 'fs_as_approval_workflows';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'workflow_key',
        'module_key',
        'branch_id',
        'entity_key',
        'name',
        'description',
        'min_amount',
        'max_amount',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
}
