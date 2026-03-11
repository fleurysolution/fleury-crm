<?php

namespace App\Services;

use App\Models\FsApprovalWorkflowModel;
use App\Models\FsApprovalWorkflowStepModel;
use App\Models\FsApprovalRequestModel;
use Config\Database;

class WorkflowEngine
{
    /**
     * Finds the applicable workflow based on module, branch, and amount.
     */
    public function resolveWorkflow(string $moduleKey, ?int $branchId = null, ?float $amount = null): ?array
    {
        $db = Database::connect();
        $builder = $db->table('fs_as_approval_workflows')
                      ->where('module_key', $moduleKey)
                      ->where('is_active', 1);

        if ($branchId) {
            $builder->groupStart()
                        ->where('branch_id', $branchId)
                        ->orWhere('branch_id IS NULL')
                    ->groupEnd();
        }

        if ($amount !== null) {
            $builder->groupStart()
                        ->where('min_amount <=', $amount)
                        ->orWhere('min_amount IS NULL')
                    ->groupEnd();
            $builder->groupStart()
                        ->where('max_amount >=', $amount)
                        ->orWhere('max_amount IS NULL')
                    ->groupEnd();
        }

        // Output prioritized list (branch specific takes precedence over global)
        $builder->orderBy('branch_id', 'DESC');
        
        return $builder->get()->getRowArray();
    }

    /**
     * Initializes an approval request and creates the first step logs.
     */
    public function submitRequest(string $moduleKey, string $entityType, int $entityId, int $userId, array $payload = [], ?int $branchId = null, ?float $amount = null): ?int
    {
        $workflow = $this->resolveWorkflow($moduleKey, $branchId, $amount);
        if (!$workflow) {
            return null; // No workflow necessary, auto-approve logic should be handled by caller
        }

        $requestModel = new FsApprovalRequestModel();
        
        $reqId = $requestModel->insert([
            'request_key' => uniqid('REQ-'),
            'workflow_id' => $workflow['id'],
            'module_key' => $moduleKey,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'requested_by' => $userId,
            'status' => 'pending',
            'current_step_no' => 1,
            'payload_json' => json_encode($payload),
            'submitted_at' => date('Y-m-d H:i:s')
        ]);

        $this->logAction($reqId, 1, $userId, 'submitted', 'Workflow Initialized');

        return $reqId;
    }

    /**
     * Checks if a user is currently delegated to act on behalf of an approver.
     */
    public function resolveDelegatee(int $userId): int
    {
        $db = Database::connect();
        $delegation = $db->table('fs_approval_delegations')
                         ->where('delegator_user_id', $userId)
                         ->where('is_active', 1)
                         ->where('start_date <=', date('Y-m-d'))
                         ->groupStart()
                            ->where('end_date >=', date('Y-m-d'))
                            ->orWhere('end_date IS NULL')
                         ->groupEnd()
                         ->get()
                         ->getRowArray();

        return $delegation ? (int)$delegation['delegatee_user_id'] : $userId;
    }

    /**
     * Writes an immutable log entry
     */
    public function logAction(int $requestId, int $stepNo, ?int $userId, string $action, string $comment = ''): void
    {
        $db = Database::connect();
        $db->table('fs_approval_logs')->insert([
            'request_id' => $requestId,
            'step_no' => $stepNo,
            'user_id' => $userId,
            'action' => $action,
            'comment' => $comment,
            'ip_address' => service('request')->getIPAddress() ?? 'CLI',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Process an approval or rejection action from a user
     */
    public function processAction(int $requestId, int $userId, string $action, string $comment = ''): bool
    {
        $requestModel = new FsApprovalRequestModel();
        $request = $requestModel->find($requestId);
        
        if (!$request || $request['status'] !== 'pending') {
            return false;
        }

        $workflowStepsModel = new FsApprovalWorkflowStepModel();
        $currentStepNo = $request['current_step_no'];
        
        $actualUserId = $this->resolveDelegatee($userId);
        
        $this->logAction($requestId, $currentStepNo, $actualUserId, $action, $comment);

        if ($action === 'rejected') {
            $requestModel->update($requestId, [
                'status' => 'rejected', 
                'completed_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->applyFinalRejection($request['module_key'], $request['entity_id']);
            return true;
        }

        if ($action === 'approved') {
            // Check if there is a next step
            $nextStep = $workflowStepsModel->where('workflow_id', $request['workflow_id'])
                                           ->where('step_no >', $currentStepNo)
                                           ->orderBy('step_no', 'ASC')
                                           ->first();
            if ($nextStep) {
                // Move to next step
                $requestModel->update($requestId, ['current_step_no' => $nextStep['step_no']]);
            } else {
                // Completed!
                $requestModel->update($requestId, [
                    'status' => 'approved', 
                    'completed_at' => date('Y-m-d H:i:s')
                ]);
                
                $this->applyFinalApproval($request['module_key'], $request['entity_id']);
            }
            return true;
        }
        
        return false;
    }

    protected function applyFinalApproval(string $moduleKey, int $entityId)
    {
        $db = Database::connect();
        if ($moduleKey === 'purchase_orders') {
            $db->table('project_purchase_orders')->where('id', $entityId)->update(['status' => 'approved']);
        } elseif ($moduleKey === 'timesheets') {
            $db->table('timesheets')->where('id', $entityId)->update([
                'status' => 'approved', 
                'approved_at' => date('Y-m-d H:i:s')
            ]);
        } elseif ($moduleKey === 'pay_apps') {
            $db->table('project_pay_apps')->where('id', $entityId)->update(['status' => 'Approved']);
        } elseif ($moduleKey === 'estimates') {
            $db->table('estimates')->where('id', $entityId)->update(['status' => 'accepted']);
        } elseif ($moduleKey === 'expenses') {
            $db->table('project_expenses')->where('id', $entityId)->update([
                'status' => 'approved',
                'approved_at' => date('Y-m-d H:i:s')
            ]);
        } elseif ($moduleKey === 'site_diaries') {
            $db->table('project_site_diaries')->where('id', $entityId)->update([
                'status' => 'Approved',
                'approved_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    protected function applyFinalRejection(string $moduleKey, int $entityId)
    {
        $db = Database::connect();
        if ($moduleKey === 'purchase_orders') {
            $db->table('project_purchase_orders')->where('id', $entityId)->update(['status' => 'rejected']);
        } elseif ($moduleKey === 'timesheets') {
            $db->table('timesheets')->where('id', $entityId)->update(['status' => 'rejected']);
        } elseif ($moduleKey === 'pay_apps') {
            $db->table('project_pay_apps')->where('id', $entityId)->update(['status' => 'Rejected']);
        } elseif ($moduleKey === 'estimates') {
            $db->table('estimates')->where('id', $entityId)->update(['status' => 'declined']);
        } elseif ($moduleKey === 'expenses') {
            $db->table('project_expenses')->where('id', $entityId)->update(['status' => 'rejected']);
        } elseif ($moduleKey === 'site_diaries') {
            $db->table('project_site_diaries')->where('id', $entityId)->update(['status' => 'Rejected']);
        }
    }
}
