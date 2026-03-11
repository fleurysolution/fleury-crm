<?php

namespace App\Models;

use CodeIgniter\Model;

class FsApprovalRequestStepModel extends Model
{
    protected $table          = 'fs_as_approval_request_steps';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'approval_request_id',
        'workflow_step_id',
        'step_no',
        'status',
        'acted_by',
        'acted_at',
        'action_note',
        'due_at',
        'created_at',
        'updated_at',
        'signed_at',
        'signature_ip',
        'signature_data',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
}
