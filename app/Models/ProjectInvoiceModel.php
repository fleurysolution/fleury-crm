<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectInvoiceModel extends ErpModel
{
    protected $table          = 'project_invoices';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'tenant_id','branch_id','project_id','contract_id','cert_id','invoice_number','direction',
        'party_name','invoice_date','due_date','subtotal','tax_amount',
        'total_amount','paid_amount','status','notes','filepath','created_by',
    ];

    public function forProject(int $projectId, string $direction = ''): array
    {
        $q = $this->where('project_id', $projectId)->where('deleted_at IS NULL');
        if ($direction) $q = $q->where('direction', $direction);
        return $q->orderBy('invoice_date', 'DESC')->findAll();
    }

    public function totalByDirection(int $projectId): array
    {
        $db   = \Config\Database::connect();
        $rows = $db->query(
            'SELECT direction, COALESCE(SUM(total_amount),0) AS total, COALESCE(SUM(paid_amount),0) AS paid FROM project_invoices WHERE project_id=? AND deleted_at IS NULL GROUP BY direction',
            [$projectId]
        )->getResult('array');
        $out = ['income'=>['total'=>0,'paid'=>0],'expense'=>['total'=>0,'paid'=>0]];
        foreach ($rows as $r) { $out[$r['direction']] = ['total'=>(float)$r['total'],'paid'=>(float)$r['paid']]; }
        return $out;
    }

    public function nextNumber(int $projectId): string
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COUNT(*)+1 AS n FROM project_invoices WHERE project_id=?', [$projectId])->getRow();
        return 'INV-' . str_pad($row->n, 4, '0', STR_PAD_LEFT);
    }
}
