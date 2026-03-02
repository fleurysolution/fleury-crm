<?php

namespace App\Controllers;

use App\Services\ApprovalService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Approval extends BaseController
{
    protected ApprovalService $approvalService;

    public function __construct()
    {
        $this->approvalService = service('approvalService');
        helper(['url', 'form']);
    }

    /**
     * List all approval requests
     */
    public function index()
    {
        $data['title'] = 'Approval Requests';
        $data['requests'] = $this->approvalService->getAllRequests();
        
        return view('approval/requests', $data);
    }

    /**
     * View a specific approval request
     */
    public function view(int $id)
    {
        $request = $this->approvalService->getRequestById($id);
        
        if (!$request) {
            return redirect()->to(site_url('approval'))->with('error', 'Request not found');
        }
        
        $data['title'] = 'View Request';
        $data['request'] = $request;
        $data['comments'] = $this->approvalService->getRequestComments($id);
        $data['steps'] = $this->approvalService->getRequestSteps($id);
        
        return view('approval/request_view', $data);
    }

    /**
     * Create a new approval request
     */
    public function create(): RedirectResponse
    {
        $title = trim((string) $this->request->getPost('title'));
        $description = trim((string) $this->request->getPost('description'));
        $workflowId = (int) $this->request->getPost('workflow_id');

        $result = $this->approvalService->createRequest($title, $description, $workflowId, user_id());

        if (!$result['success']) {
            return redirect()->to(site_url('approval'))
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('approval/view/' . $result['request_id']))
            ->with('status', 'Request created successfully');
    }

    /**
     * Approve an approval request step
     */
    public function approve(int $id): RedirectResponse
    {
        $note = trim((string) $this->request->getPost('note'));
        $signature = $this->request->getPost('signature_data');

        $result = $this->approvalService->approveRequest($id, user_id(), $note, $signature);

        if (!$result['success']) {
            return redirect()->to(site_url('approval/view/' . $id))
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('approval/view/' . $id))
            ->with('status', 'Request approved successfully');
    }

    /**
     * Reject an approval request step
     */
    public function reject(int $id): RedirectResponse
    {
        $note = trim((string) $this->request->getPost('note'));

        $result = $this->approvalService->rejectRequest($id, user_id(), $note);

        if (!$result['success']) {
            return redirect()->to(site_url('approval/view/' . $id))
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('approval/view/' . $id))
            ->with('status', 'Request rejected');
    }

    /**
     * Add a comment to an approval request
     */
    public function addComment(int $id): RedirectResponse
    {
        $comment = trim((string) $this->request->getPost('comment'));

        $result = $this->approvalService->addComment($id, user_id(), $comment);

        if (!$result['success']) {
            return redirect()->to(site_url('approval/view/' . $id))
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('approval/view/' . $id))
            ->with('status', 'Comment added successfully');
    }
}
