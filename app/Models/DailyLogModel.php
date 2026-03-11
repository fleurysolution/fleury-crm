<?php

namespace App\Models;

use App\Models\ErpModel;

class DailyLogModel extends ErpModel
{
    protected $table          = 'fs_daily_logs';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    
    // Automatically injected by ErpModel, but good to whitelist
    protected $allowedFields  = [
        'tenant_id', 'branch_id', 'project_id', 'date', 
        'weather_conditions', 'temperature', 'site_conditions', 
        'notes', 'status', 'created_by', 'approved_by'
    ];
}
