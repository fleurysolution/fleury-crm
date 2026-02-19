<?php namespace App\Models;

class Approval_model extends Crud_model
{
    protected $table = 'approvals';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'module', 'module_id', 'requested_by', 'approved_by',
        'status', 'comments', 'created_at', 'approved_at'
    ];
      function __construct() {
        $this->table = 'approval_settings';
        parent::__construct($this->table);
    }


    public function getApprovalStatus($module, $moduleId)
    {
        return $this->where(['module' => $module, 'module_id' => $moduleId])
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    public function createApprovalRequest($module, $moduleId, $requestedBy)
    {
        return $this->insert([
            'module' => $module,
            'module_id' => $moduleId,
            'requested_by' => $requestedBy,
            'status' => 'pending'
        ]);
    }

    public function approve($id, $approvedBy, $comments = '')
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'comments' => $comments,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function reject($id, $approvedBy, $comments = '')
    {
        return $this->update($id, [
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'comments' => $comments,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }
}
