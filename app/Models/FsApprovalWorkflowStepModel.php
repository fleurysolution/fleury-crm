<?php

namespace App\Models;

use CodeIgniter\Model;

class FsApprovalWorkflowStepModel extends Model
{
    protected $table         = 'fs_as_approval_workflow_steps';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'workflow_id',
        'step_no',
        'step_name',
        'approver_type',
        'approver_role_id',
        'approver_user_id',
        'min_approvals',
        'is_mandatory',
        'sla_hours',
        'escalation_role_id',
        'escalation_user_id',
        'created_at',
    ];

    public $useTimestamps = false;
}
