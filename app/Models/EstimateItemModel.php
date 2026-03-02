<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateItemModel extends Model
{
    protected $table          = 'project_estimate_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // standard relational table, items hard delete

    protected $allowedFields = [
        'estimate_id',
        'cost_code',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost'
    ];

    /**
     * Get all line items for a specific estimate.
     */
    public function forEstimate(int $estimateId): array
    {
        return $this->where('estimate_id', $estimateId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
