<?php

namespace App\Controllers;

use App\Services\WorkflowEngine;
use Config\Database;

class Approvals extends BaseAppController
{
    /**
     * GET /approvals
     * Fetch all pending approval requests assigned to the current user (or their roles)
     */
    public function index()
    {
        $db = Database::connect();
        $userId = $this->currentUser['id'];
        $userRoles = session('user_roles') ?? [];

        // Check if user is delegated to approve on behalf of someone else
        $delegatorUserIds = [$userId];
        $delegations = $db->table('fs_approval_delegations')
                          ->where('delegatee_user_id', $userId)
                          ->where('is_active', 1)
                          ->where('start_date <=', date('Y-m-d'))
                          ->groupStart()
                             ->where('end_date >=', date('Y-m-d'))
                             ->orWhere('end_date IS NULL')
                          ->groupEnd()
                          ->get()->getResultArray();
                          
        foreach ($delegations as $d) {
            $delegatorUserIds[] = (int)$d['delegator_user_id'];
        }

        $builder = $db->table('fs_as_approval_requests r')
                      ->select('r.*, w.name as workflow_name, s.step_name')
                      ->join('fs_as_approval_workflows w', 'w.id = r.workflow_id')
                      ->join('fs_as_approval_workflow_steps s', 's.workflow_id = w.id AND s.step_no = r.current_step_no')
                      ->where('r.status', 'pending');

        $builder->groupStart();
        if (!empty($delegatorUserIds)) {
             $builder->whereIn('s.approver_user_id', $delegatorUserIds);
        }
        if (!empty($userRoles)) {
            // we need the role IDs instead of slugs, but userModel might just give us slugs.
            // Let's lookup role IDs matching the user's role slugs to be safe
            $roleIds = $db->table('roles')->whereIn('slug', $userRoles)->get()->getResultArray();
            $rIds = array_column($roleIds, 'id');
            if(!empty($rIds)) {
                $builder->orWhereIn('s.approver_role_id', $rIds);
            }
        }
        $builder->groupEnd();

        $pendingApprovals = $builder->get()->getResultArray();

        // In a real app we'd render an HTML View, but let's return JSON for API consumption
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $pendingApprovals
        ]);
    }

    /**
     * POST /approvals/(:num)/action
     * Action is supplied via form data: 'action' => 'approved' or 'rejected'
     */
    public function processAction(int $requestId)
    {
        $action = $this->request->getPost('action');
        $comment = (string)$this->request->getPost('comment');

        if (!in_array($action, ['approved', 'rejected'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid action']);
        }

        $workflow = new WorkflowEngine();
        $success = $workflow->processAction($requestId, $this->currentUser['id'], $action, $comment);

        if ($success) {
            return $this->response->setJSON(['status' => 'success', 'message' => "Request {$action} successfully."]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to process request. Invalid ID, not pending, or unauthorized.']);
    }
}
