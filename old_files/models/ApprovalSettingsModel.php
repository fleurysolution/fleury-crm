<?php

namespace App\Models;

class ApprovalSettingsModel extends Crud_model
{
    protected $table = null;

    function __construct() {
        $this->table = 'approval_settings';
        parent::__construct($this->table);
    }

    // protected $table = 'pcm_approval_settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'module',
        'requester_role',
        'approver_role',
        'requester_user_id',
        'approver_user_id',
        'hierarchy_level',
        'require_all_approvers',
        'active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    public function getModulesList()
    {
        // You can extend this list dynamically if modules table exists
        return [
            'dashboard',
            'events',
            'marketing_automation',
            'flexiblebackup',
            'sales_agent',
            'accounting',
            'clients',
            'inventory',
            'polls',
            'api_management',
            'manufacturing',
            'google_meet',
            'purchase',
            'assets',
            'projects',
            'tasks',
            'recruitments',
            'hr_payroll',
            'leads',
            'hr_records',
            'subscriptions',
            'sales',
            'prospects',
            'banner_manager',
            'notes',
            'messages',
            'team',
            'helpdesk_tickets',
            'expenses',
            'reports',
            'files',
            'help_support',
            'settings'
        ];
    }
}
