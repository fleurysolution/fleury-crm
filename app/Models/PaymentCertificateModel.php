<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentCertificateModel extends Model
{
    protected $table          = 'payment_certificates';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'project_id','contract_id','cert_number','period_from','period_to',
        'gross_amount','retention_amount','net_amount','status',
        'submitted_by','approved_by','approved_at','paid_at','notes',
    ];

    public function forProject(int $projectId): array
    {
        return $this->select('payment_certificates.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS submitter_name, CONCAT(a.first_name, " ", a.last_name) AS approver_name')
            ->join('fs_users', 'fs_users.id = payment_certificates.submitted_by', 'left')
            ->join('fs_users AS a', 'a.id = payment_certificates.approved_by', 'left')
            ->where('payment_certificates.project_id', $projectId)
            ->where('payment_certificates.deleted_at IS NULL')
            ->orderBy('cert_number', 'DESC')
            ->findAll();
    }

    public function totalPaid(int $projectId): float
    {
        $db = \Config\Database::connect();
        $r  = $db->query(
            'SELECT COALESCE(SUM(net_amount),0) AS t FROM payment_certificates WHERE project_id=? AND status="paid" AND deleted_at IS NULL',
            [$projectId]
        )->getRow();
        return (float)($r->t ?? 0);
    }

    public function nextNumber(int $projectId): string
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COUNT(*)+1 AS n FROM payment_certificates WHERE project_id=?', [$projectId])->getRow();
        return 'IPC-' . str_pad($row->n, 3, '0', STR_PAD_LEFT);
    }
}
