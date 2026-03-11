<?php

namespace App\Models;

use CodeIgniter\Model;

class ErpModel extends Model
{
    /**
     * Set this to false in child models if a specific table shouldn't enforce branch_id
     * (e.g. global settings or tenant-level only configurations).
     */
    protected $enforceBranchLinkage = true;

    protected $beforeInsert = ['checkIsolationFields'];
    protected $beforeUpdate = ['checkIsolationFieldsUpdate'];
    protected $beforeFind   = ['applyAbacScope'];

    protected function applyAbacScope(array $data)
    {
        $session = session();
        $tenantId = $session->get('tenant_id');

        // 1. Priority: Tenant Isolation (Mandatory for SaaS)
        // If not logged in (CLI), we don't apply filters here but checkBranchId will catch inserts.
        if ($session->get('is_logged_in')) {
            $roles = $session->get('user_roles') ?? [];
            
            // Platform admin (tenant_id 1 with superadmin role) can see across tenants if needed, 
            // but usually they stay in their container.
            if ($tenantId == 1 && in_array('superadmin', $roles)) {
                // No tenant filter
            } else if ($tenantId) {
                $this->where($this->table . '.tenant_id', $tenantId);
            }
        }

        if (!$this->enforceBranchLinkage) {
            return $data;
        }

        if ($session->get('is_logged_in')) {
            $roles = $session->get('user_roles') ?? [];
            if (in_array('admin', $roles) || in_array('superadmin', $roles)) {
                return $data; // Admins see all branches within their tenant
            }

            $geoAccess = $session->get('geo_access_permission');
            $branchId  = $session->get('branch_id');

            if ($geoAccess === 'branch' && $branchId) {
                $this->where($this->table . '.branch_id', $branchId);
            }
        }

        return $data;
    }

    public function builder(string $table = null)
    {
        $builder = parent::builder($table);
        $session = session();
        $tenantId = $session->get('tenant_id');

        if ($session->get('is_logged_in')) {
            $roles = $session->get('user_roles') ?? [];
            if ($tenantId == 1 && in_array('superadmin', $roles)) {
                // No tenant filter
            } else if ($tenantId) {
                $tableName = $table ?? $this->table;
                $builder->where($tableName . '.tenant_id', $tenantId);
            }

            if ($this->enforceBranchLinkage) {
                if (!in_array('admin', $roles) && !in_array('superadmin', $roles)) {
                    $geoAccess = $session->get('geo_access_permission');
                    $branchId  = $session->get('branch_id');

                    if ($geoAccess === 'branch' && $branchId) {
                        $tableName = $table ?? $this->table;
                        $builder->where($tableName . '.branch_id', $branchId);
                    }
                }
            }
        }

        return $builder;
    }

    protected function checkIsolationFields(array $data)
    {
        $session = session();
        $tenantId = $session->get('tenant_id');

        // Auto-assign tenant_id from session if missing
        if (!isset($data['data']['tenant_id']) && $tenantId) {
            $data['data']['tenant_id'] = $tenantId;
        }

        if (!isset($data['data']['tenant_id']) || empty($data['data']['tenant_id'])) {
            $this->logAndThrow("Validation failure: Mandatory tenant_id missing on insert for table '{$this->table}'.");
        }

        if (!$this->enforceBranchLinkage) {
            return $data;
        }

        if (!isset($data['data']['branch_id']) || empty($data['data']['branch_id'])) {
            $this->logAndThrow("Validation failure: Mandatory branch_id missing on insert for table '{$this->table}'.");
        }
        return $data;
    }

    protected function checkIsolationFieldsUpdate(array $data)
    {
        if (array_key_exists('tenant_id', $data['data']) && empty($data['data']['tenant_id'])) {
            $this->logAndThrow("Validation failure: Cannot update tenant_id to empty for table '{$this->table}'.");
        }

        if (!$this->enforceBranchLinkage) {
            return $data;
        }

        if (array_key_exists('branch_id', $data['data']) && empty($data['data']['branch_id'])) {
            $this->logAndThrow("Validation failure: Cannot update branch_id to empty for table '{$this->table}'.");
        }
        return $data;
    }

    private function logAndThrow(string $message)
    {
        $userId = session()->has('user') ? (session('user')['id'] ?? 'system') : 'system';
        $timestamp = date('Y-m-d H:i:s');
        
        $logMessage = "[{$timestamp}] User ID: {$userId} - {$message}";
        log_message('critical', $logMessage);
        
        throw new \RuntimeException($message);
    }
}
