<?php

namespace App\Controllers;

use App\Models\SubmittalModel;
use App\Models\SubmittalRevisionModel;
use App\Models\ProjectModel;
use App\Models\ProjectMemberModel;

class Submittals extends BaseAppController
{
    protected SubmittalModel $submittalModel;
    protected ProjectModel $projectModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->submittalModel = new SubmittalModel();
        $this->projectModel   = new ProjectModel();
    }

    public function index(int $projectId)
    {
        $submittals = $this->submittalModel->where('project_id', $projectId)->orderBy('created_at', 'DESC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $submittals]);
    }

    public function store(int $projectId)
    {
        $subNumber = $this->request->getPost('submittal_number');
        $title     = $this->request->getPost('title');

        if (empty($subNumber) || empty($title)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Submittal Number and Title are required.'])->setStatusCode(400);
        }

        $project = $this->projectModel->find($projectId);

        $data = [
            'tenant_id'         => $project['tenant_id'],
            'branch_id'         => $project['branch_id'],
            'project_id'        => $projectId,
            'submittal_number'  => $this->request->getPost('submittal_number'),
            'spec_section'      => $this->request->getPost('spec_section'),
            'title'             => $this->request->getPost('title'),
            'description'       => $this->request->getPost('description'),
            'status'            => 'submitted',
            'due_date'          => $this->request->getPost('due_date'),
            'assigned_to'       => $this->request->getPost('assigned_to'),
            'created_by'        => session('user_id'),
        ];

        try {
            $id = $this->submittalModel->insert($data);
            session()->setFlashdata('message', 'Submittal created successfully.');
            return $this->response->setJSON(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()])->setStatusCode(400);
        }
    }

    public function show(int $submittalId)
    {
        $submittal = $this->submittalModel
                        ->select('fs_submittals.*, 
                                 CONCAT(u1.first_name, " ", u1.last_name) AS submitter_name,
                                 CONCAT(u2.first_name, " ", u2.last_name) AS reviewer_name')
                        ->join('fs_users u1', 'u1.id = fs_submittals.created_by', 'left')
                        ->join('fs_users u2', 'u2.id = fs_submittals.assigned_to', 'left')
                        ->find($submittalId);

        if (!$submittal) {
            return $this->response->setStatusCode(404);
        }

        $project = $this->projectModel->find($submittal['project_id']);
        $revisions = (new \App\Models\SubmittalRevisionModel())->forSubmittal($submittalId);
        $members = (new \App\Models\ProjectMemberModel())->getMembers($submittal['project_id']);

        return $this->render('submittals/show', [
            'title'     => 'Submittal ' . $submittal['submittal_number'],
            'submittal' => $submittal,
            'project'   => $project,
            'revisions' => $revisions,
            'members'   => $members
        ]);
    }

    public function review(int $submittalId)
    {
        $submittal = $this->submittalModel->find($submittalId);
        if (!$submittal) {
            return $this->response->setStatusCode(404);
        }

        $status    = $this->request->getPost('status');
        $forwardTo = $this->request->getPost('forward_to');
        $notes     = $this->request->getPost('notes');
        $sigData   = $this->request->getPost('signature_data');

        // 1. Create a new revision/review record
        $revModel = new \App\Models\SubmittalRevisionModel();
        $revModel->insert([
            'submittal_id'  => $submittalId,
            'revision_no'   => $submittal['revision'] + 1,
            'status'        => $status,
            'reviewer_id'   => session('user_id'),
            'reviewed_at'   => date('Y-m-d H:i:s'),
            'notes'         => $notes,
            'signature_data'=> $sigData,
            'signature_ip'  => $this->request->getIPAddress(),
            'signed_at'     => $sigData ? date('Y-m-d H:i:s') : null,
        ]);

        // 2. Update the main submittal status
        $updateData = [
            'status'   => $status,
            'revision' => $submittal['revision'] + 1
        ];

        // 3. Handle forwarding
        if (!empty($forwardTo)) {
            $updateData['status']      = 'under_review';
            $updateData['assigned_to'] = $forwardTo;
        }

        $this->submittalModel->update($submittalId, $updateData);

        return $this->response->setJSON(['success' => true]);
    }
}
