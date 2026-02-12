<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsApprovalWorkflowModel extends Model
{
    protected $table          = 'fs_as_approval_workflows';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'workflow_key',
        'module_key',
        'entity_key',
        'name',
        'description',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
}
