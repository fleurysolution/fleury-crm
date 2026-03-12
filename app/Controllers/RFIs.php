<?php

namespace App\Controllers;

use App\Models\RfiModel;
use App\Models\RFIResponseModel;
use App\Models\ProjectModel;

class RFIs extends BaseAppController
{
    protected RfiModel $rfiModel;
    protected RFIResponseModel $replyModel;
    protected ProjectModel $projectModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->rfiModel     = new RfiModel();
        $this->replyModel   = new RFIResponseModel();
        $this->projectModel = new ProjectModel();
    }

    public function index(int $projectId)
    {
        $rfis = $this->rfiModel->where('project_id', $projectId)->orderBy('created_at', 'DESC')->findAll();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'data' => $rfis]);
        }

        return $this->render('rfis/index', [
            'title' => 'Project RFIs',
            'rfis'  => $rfis,
            'projectId' => $projectId
        ]);
    }

    public function globalIndex()
    {
        $rfis = $this->rfiModel->orderBy('created_at', 'DESC')->findAll();
        
        return $this->render('rfis/index', [
            'title' => 'RFIs',
            'rfis'  => $rfis
        ]);
    }

    public function store(int $projectId)
    {
        $rfiNumber = $this->request->getPost('rfi_number');
        $title     = $this->request->getPost('title');

        if (empty($rfiNumber) || empty($title)) {
            return $this->response->setJSON(['success' => false, 'error' => 'RFI Number and Title are required.'])->setStatusCode(400);
        }

        $project = $this->projectModel->find($projectId);

        $data = [
            'tenant_id'         => $project['tenant_id'],
            'branch_id'         => $project['branch_id'],
            'project_id'        => $projectId,
            'rfi_number'        => $this->request->getPost('rfi_number'),
            'title'             => $this->request->getPost('title'),
            'description'       => $this->request->getPost('description'),
            'proposed_solution' => $this->request->getPost('proposed_solution'),
            'discipline'        => $this->request->getPost('discipline'),
            'priority'          => $this->request->getPost('priority'),
            'status'            => 'open',
            'due_date'          => $this->request->getPost('due_date'),
            'area_id'           => $this->request->getPost('area_id') ?: null,
            'assigned_to'       => $this->request->getPost('assigned_to'),
            'created_by'        => session('user_id'),
        ];

        try {
            $id = $this->rfiModel->insert($data);
            session()->setFlashdata('message', 'RFI created successfully.');
            return $this->response->setJSON(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()])->setStatusCode(400);
        }
    }

    public function show(int $rfiId)
    {
        $rfi = $this->rfiModel
                    ->select('fs_rfis.*, 
                             CONCAT(u1.first_name, " ", u1.last_name) AS submitter_name,
                             CONCAT(u2.first_name, " ", u2.last_name) AS assignee_name')
                    ->join('fs_users u1', 'u1.id = fs_rfis.created_by', 'left')
                    ->join('fs_users u2', 'u2.id = fs_rfis.assigned_to', 'left')
                    ->find($rfiId);

        if (!$rfi) {
            return $this->response->setStatusCode(404);
        }

        $project = $this->projectModel->find($rfi['project_id']);
        $replies = $this->replyModel->select('fs_rfi_replies.*, CONCAT(u.first_name, " ", u.last_name) AS author_name')
                        ->join('fs_users u', 'u.id = fs_rfi_replies.user_id', 'left')
                        ->where('rfi_id', $rfiId)
                        ->orderBy('created_at', 'ASC')
                        ->findAll();
        
        return $this->render('rfis/show', [
            'title'     => 'RFI ' . $rfi['rfi_number'],
            'rfi'       => $rfi,
            'project'   => $project,
            'responses' => $replies
        ]);
    }

    public function respond(int $rfiId)
    {
        // First, check if user can access this RFI via ErpModel scope
        $rfi = $this->rfiModel->find($rfiId);
        if (!$rfi) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->replyModel->insert([
            'rfi_id' => $rfiId,
            'user_id' => session('user_id'),
            'reply' => $this->request->getPost('body'),
            'is_official_answer' => $this->request->getPost('is_official') ? 1 : 0
        ]);

        return $this->response->setJSON(['success' => true, 'reply_id' => $id]);
    }
}
