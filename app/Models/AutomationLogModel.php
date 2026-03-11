<?php

namespace App\Models;

use CodeIgniter\Model;

class AutomationLogModel extends Model
{
    protected $table          = 'automation_logs';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['tenant_id', 'rule_id', 'entity_id', 'status', 'message', 'executed_at'];
    protected $useTimestamps   = false;
}
