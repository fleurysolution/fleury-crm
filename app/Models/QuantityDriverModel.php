<?php

namespace App\Models;

use App\Models\ErpModel;

class QuantityDriverModel extends ErpModel
{
    protected $table          = 'project_drivers';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields   = [
        'project_id', 'name', 'unit', 'value', 'description'
    ];

    public function getForProject(int $projectId)
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
