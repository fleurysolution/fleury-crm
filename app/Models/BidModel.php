<?php

namespace App\Models;

use CodeIgniter\Model;

class BidModel extends Model
{
    protected $table          = 'project_bids';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'trade_package',
        'vendor_name',
        'bid_amount',
        'status',
        'remarks',
        'quote_filepath',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all bids for a project, optionally mapped with uploader name.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_bids.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS creator_name')
            ->join('fs_users', 'fs_users.id = project_bids.created_by', 'left')
            ->where('project_bids.project_id', $projectId)
            ->where('project_bids.deleted_at IS NULL')
            ->orderBy('project_bids.trade_package', 'ASC')
            ->orderBy('project_bids.bid_amount', 'ASC')
            ->findAll();
    }
}
