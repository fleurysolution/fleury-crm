<?php

namespace App\Models;

use CodeIgniter\Model;

class InspectionItemModel extends Model
{
    protected $table          = 'fs_inspection_items';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = false; // no deleted_at field in migration
    protected $allowedFields  = [
        'inspection_id', 'description', 'status', 'remarks'
    ];

    public function forInspection(int $inspectionId): array
    {
        return $this->where('inspection_id', $inspectionId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }
}
