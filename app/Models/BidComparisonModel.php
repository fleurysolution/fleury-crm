<?php

namespace App\Models;

class BidComparisonModel extends ErpModel
{
    protected $table          = 'project_bid_comparisons';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = false;
    protected $allowedFields  = [
        'project_id', 'boq_item_id', 'vendor_id', 
        'quoted_amount', 'is_leveled', 'scope_notes'
    ];

    public function getLeveledBids(int $projectId): array
    {
        return $this->where('project_id', $projectId)->where('is_leveled', 1)->findAll();
    }
}
