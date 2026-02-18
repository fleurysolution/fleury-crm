<?php

namespace App\Services;

use App\Models\FsApprovalRequestModel;
use App\Models\FsApprovalRequestStepModel;
use App\Models\FsAuditEventModel;
use App\Libraries\ApprovalStatus;
use Config\Database;

class ApprovalService
{
    protected FsApprovalRequestModel $requests;
    protected FsApprovalRequestStepModel $steps;
    protected FsAuditEventModel $audit;

    public function __construct()
    {
        $this->requests = new FsApprovalRequestModel();
        $this->steps    = new FsApprovalRequestStepModel();
        $this->audit    = new FsAuditEventModel();
    }

    public function listRequests(int $limit = 50): array
    {
        return $this->requests
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    public function getRequestWithSteps(int $requestId): ?array
    {
        $request = $this->requests->find($requestId);
        if (!$request) {
            return null;
        }

        $steps = $this->steps->where('approval_request_id', $requestId)->orderBy('step_no', 'ASC')->findAll();

        $request['steps'] = $steps;
        return $request;
    }

    public function actOnCurrentStep(int $requestId, int $actorUserId, string $actionStatus, ?string $note = null): array
    {
        if (!in_array($actionStatus, [ApprovalStatus::STEP_APPROVED, ApprovalStatus::STEP_REJECTED], true)) {
            return ['success' => false, 'errors' => ['Invalid action status.']];
        }

        $request = $this->requests->find($requestId);
        if (!$request) {
            return ['success' => false, 'errors' => ['Approval request not found.']];
        }

        $currentStepNo = (int)($request['current_step_no'] ?? 1);

        $step = $this->steps
            ->where('approval_request_id', $requestId)
            ->where('step_no', $currentStepNo)
            ->first();

        if (!$step) {
            return ['success' => false, 'errors' => ['Current approval step not found.']];
        }

        if (($step['status'] ?? '') !== ApprovalStatus::STEP_PENDING) {
            return ['success' => false, 'errors' => ['Current step is already processed.']];
        }

        // transition validation
        if (!ApprovalStatus::canTransitionStep($step['status'], $actionStatus)) {
            return ['success' => false, 'errors' => ['Invalid step transition.']];
        }

        $db = Database::connect();
        $db->transStart();

        // Update step
        $this->steps->update((int)$step['id'], [
            'status'     => $actionStatus,
            'acted_by'   => $actorUserId,
            'acted_at'   => date('Y-m-d H:i:s'),
            'action_note'=> $note,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($actionStatus === ApprovalStatus::STEP_REJECTED) {
            // whole request rejected
            if (!ApprovalStatus::canTransitionRequest((string)$request['status'], ApprovalStatus::REQUEST_REJECTED)) {
                $db->transRollback();
                return ['success' => false, 'errors' => ['Invalid request transition to rejected.']];
            }

            $this->requests->update($requestId, [
                'status'       => ApprovalStatus::REQUEST_REJECTED,
                'completed_at' => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            $message = 'Request rejected successfully.';
        } else {
            // approved current step -> check next pending step
            $nextStep = $this->steps
                ->where('approval_request_id', $requestId)
                ->where('step_no', $currentStepNo + 1)
                ->first();

            if ($nextStep) {
                $this->requests->update($requestId, [
                    'current_step_no' => $currentStepNo + 1,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $message = 'Step approved. Moved to next step.';
            } else {
                // last step approved => request approved
                if (!ApprovalStatus::canTransitionRequest((string)$request['status'], ApprovalStatus::REQUEST_APPROVED)) {
                    $db->transRollback();
                    return ['success' => false, 'errors' => ['Invalid request transition to approved.']];
                }

                $this->requests->update($requestId, [
                    'status'       => ApprovalStatus::REQUEST_APPROVED,
                    'completed_at' => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
                $message = 'Request approved successfully.';
            }
        }

        // audit
        $this->audit->insert([
            'event_key'    => 'approval.request.action',
            'module_key'   => 'approval',
            'actor_user_id'=> $actorUserId,
            'target_type'  => 'approval_request',
            'target_id'    => (string)$requestId,
            'ip_address'   => null,
            'user_agent'   => null,
            'metadata_json'=> json_encode([
                'step_no' => $currentStepNo,
                'action'  => $actionStatus,
                'note'    => $note,
            ], JSON_UNESCAPED_UNICODE),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return ['success' => false, 'errors' => ['Unable to process approval action.']];
        }

        return ['success' => true, 'message' => $message];
    }
}
