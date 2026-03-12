<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractAmendmentModel extends Model
{
    protected $table         = 'project_contract_amendments';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'contract_id','amendment_no','title','description',
        'value_change','time_change','status','approved_by','approved_at',
        'signed_at', 'signature_ip', 'signature_data',
    ];

    public function forContract(int $contractId): array
    {
        return $this->select('project_contract_amendments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS approver_name')
            ->join('fs_users', 'fs_users.id = project_contract_amendments.approved_by', 'left')
            ->where('contract_id', $contractId)
            ->orderBy('amendment_no')
            ->findAll();
    }

    public function totalApprovedChange(int $contractId): float
    {
        $db = \Config\Database::connect();
        
        // 1. Traditional Amendments
        $amendments  = $db->query(
            'SELECT COALESCE(SUM(value_change),0) AS t FROM project_contract_amendments WHERE contract_id=? AND status="approved"',
            [$contractId]
        )->getRow();
        
        // 2. Linked Project Change Orders
        $projectCOs = $db->query(
            'SELECT COALESCE(SUM(amount),0) AS t FROM change_orders WHERE contract_id=? AND status="approved"',
            [$contractId]
        )->getRow();

        return (float)($amendments->t ?? 0) + (float)($projectCOs->t ?? 0);
    }
}
