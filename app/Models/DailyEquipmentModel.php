<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyEquipmentModel extends Model
{
    protected $table          = 'fs_daily_equipment';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = false;
    
    protected $allowedFields  = [
        'log_id', 'equipment_type', 'hours_used', 'status', 'created_at'
    ];
}
