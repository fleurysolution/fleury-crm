<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsAuditEventModel extends Model
{
    protected $table         = 'fs_as_audit_events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'event_key',
        'module_key',
        'actor_user_id',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'metadata_json',
        'created_at',
    ];

    public $useTimestamps = false;
}
