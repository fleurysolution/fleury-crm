<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsApprovalRequestModel extends Model
{
    protected $table          = 'fs_as_approval_requests';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'request_key',
        'workflow_id',
        'module_key',
        'entity_type',
        'entity_id',
        'requested_by',
        'status',
        'current_step_no',
        'payload_json',
        'submitted_at',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
}
