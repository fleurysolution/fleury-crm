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
        $r  = $db->query(
            'SELECT COALESCE(SUM(value_change),0) AS t FROM project_contract_amendments WHERE contract_id=? AND status="approved"',
            [$contractId]
        )->getRow();
        return (float)($r->t ?? 0);
    }
}
