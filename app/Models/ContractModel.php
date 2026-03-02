<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractModel extends Model
{
    protected $table          = 'project_contracts';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','contract_number','title','type','contractor_name','client_id',
        'status','scope','value','currency','retention_pct','start_date','end_date',
        'signed_by','signed_at','filepath','created_by',
        'client_signed_at','client_ip_address','client_signature_data',
        'contractor_signed_at','contractor_ip_address','contractor_signature_data'
    ];

    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function totalValue(int $projectId): float
    {
        $db = \Config\Database::connect();
        $r  = $db->query(
            'SELECT COALESCE(SUM(value),0) AS total FROM project_contracts WHERE project_id=? AND status!="terminated" AND deleted_at IS NULL',
            [$projectId]
        )->getRow();
        return (float)($r->total ?? 0);
    }

    public function nextNumber(int $projectId): string
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COUNT(*)+1 AS n FROM project_contracts WHERE project_id=?', [$projectId])->getRow();
        return 'CON-' . str_pad($row->n, 4, '0', STR_PAD_LEFT);
    }
}
