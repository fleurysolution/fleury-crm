<?php

namespace App\Models;

use CodeIgniter\Model;

class MeetingModel extends Model
{
    protected $table          = 'meetings';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = [
        'tenant_id', 'project_id', 'title', 'meeting_date', 
        'meeting_time', 'location', 'agenda', 'minutes', 'status'
    ];
    protected $useTimestamps   = true;

    public function getForProject(int $projectId, ?int $tenantId)
    {
        return $this->where('project_id', $projectId)
                    ->where('tenant_id', $tenantId)
                    ->orderBy('meeting_date', 'DESC')
                    ->findAll();
    }
}
