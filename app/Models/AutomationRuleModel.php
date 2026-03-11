<?php

namespace App\Models;

use CodeIgniter\Model;

class AutomationRuleModel extends Model
{
    protected $table          = 'automation_rules';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = [
        'tenant_id', 'name', 'trigger_type', 'trigger_object', 
        'conditions', 'action_type', 'action_config', 'is_active'
    ];
    protected $useTimestamps   = true;

    public function getActiveRules(int $tenantId, string $triggerType, string $triggerObject)
    {
        return $this->where('tenant_id', $tenantId)
                    ->where('trigger_type', $triggerType)
                    ->where('trigger_object', $triggerObject)
                    ->where('is_active', 1)
                    ->findAll();
    }
}
