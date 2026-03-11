<?php

namespace App\Models;

// No direct branch_id on manpower/equipment, so we can just use regular Model
// The branch scoping should be enforced via the paren log_id check
use CodeIgniter\Model;

class DailyManpowerModel extends Model
{
    protected $table          = 'fs_daily_manpower';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = false; // We only have created_at mapped, will manage manually or let DB insert current_timestamp
    
    protected $allowedFields  = [
        'log_id', 'trade_or_contractor', 'worker_count', 'hours', 'notes', 'created_at'
    ];
}
