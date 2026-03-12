<?php

namespace App\Models;

use CodeIgniter\Model;

class SecurityLogModel extends Model
{
    protected $table         = 'security_log';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'event_type', 'severity', 'description', 
        'ip_address', 'user_agent', 'details', 'created_at'
    ];

    /**
     * Log a security event.
     */
    public static function record(
        ?int   $userId,
        string $eventType,
        string $severity,
        string $description,
        array  $details = []
    ): void {
        $m = new self();
        $request = \Config\Services::request();
        
        $m->insert([
            'user_id'     => $userId,
            'event_type'  => $eventType,
            'severity'    => $severity,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $request->getUserAgent()->getAgentString(),
            'details'     => !empty($details) ? json_encode($details) : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        // If severity is critical, we could trigger an alert/email here.
        if ($severity === 'critical') {
            log_message('critical', "CRITICAL SECURITY EVENT: $description [User: $userId]");
        }
    }
}
