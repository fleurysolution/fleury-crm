<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectBidModel extends ErpModel
{
    protected $table          = 'project_bids';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'tenant_id',
        'branch_id',
        'project_id',
        'trade_package',
        'vendor_name',
        'bid_amount',
        'status',
        'remarks',
        'quote_filepath',
        'created_by',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all bids for a specific project.
     */
    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
                    ->where('deleted_at IS NULL')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
