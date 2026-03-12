<?php

namespace App\Models;

class ProcurementModel extends ErpModel
{
    protected $table          = 'project_procurement_items';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'project_id', 'task_id', 'item_name', 'vendor_id', 
        'status', 'lead_time_days', 'expected_on_site', 
        'actual_arrival', 'tracking_url', 'notes'
    ];

    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)->findAll();
    }
}
