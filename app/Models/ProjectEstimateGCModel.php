<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectEstimateGCModel extends Model
{
    protected $table          = 'project_estimate_gcs';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = [
        'estimate_id', 'category', 'description', 'amount'
    ];
    protected $useTimestamps   = true;

    public function forEstimate(int $estimateId)
    {
        return $this->where('estimate_id', $estimateId)
                    ->orderBy('category', 'ASC')
                    ->findAll();
    }
}
