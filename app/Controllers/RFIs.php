<?php

namespace App\Controllers;

use App\Models\RFIModel;
use App\Models\RFIResponseModel;
use App\Models\ProjectModel;
use App\Models\AreaModel;

class RFIs extends BaseAppController
{
    /**
     * GET /projects/:id/rfis — list all RFIs for a project
     */
    public function index(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $rfiModel = new RFIModel();
        $rfis     = $rfiModel->forProject($projectId);
        $counts   = $rfiModel->statusCounts($projectId);

        return $this->render('rfis/index', [
            'project' => $project,
            'rfis'    => $rfis,
            'counts'  => $counts,
        ]);
    }

    /**
     * GET /rfis/:id — RFI detail page
     */
    public function show(int $id): string
    {
        $rfiModel  = new RFIModel();
        $rfi       = $rfiModel->find($id);
        if (!$rfi) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $project   = (new ProjectModel())->find($rfi['project_id']);
        $responses = (new RFIResponseModel())->forRFI($id);
        $areas     = (new AreaModel())->where('project_id', $rfi['project_id'])->findAll();

        return $this->render('rfis/show', [
            'project'   => $project,
            'rfi'       => $rfi,
            'responses' => $responses,
            'areas'     => $areas,
        ]);
    }

    /**
     * POST /projects/:id/rfis — create new RFI
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $rfiModel = new RFIModel();
        $data     = [
            'project_id'   => $projectId,
            'rfi_number'   => $rfiModel->nextNumber($projectId),
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'discipline'   => $this->request->getPost('discipline'),
            'priority'     => $this->request->getPost('priority') ?: 'medium',
            'status'       => 'submitted',
            'submitted_by' => $this->currentUser['id'],
            'assigned_to'  => $this->request->getPost('assigned_to') ?: null,
            'area_id'      => $this->request->getPost('area_id')     ?: null,
            'due_date'     => $this->request->getPost('due_date')    ?: null,
        ];

        $id = $rfiModel->insert($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'id' => $id, 'rfi_number' => $data['rfi_number']]);
        }
        return redirect()->to(site_url("rfis/{$id}"))->with('success', 'RFI ' . $data['rfi_number'] . ' submitted.');
    }

    /**
     * POST /rfis/:id/respond — add response/reply
     */
    public function respond(int $id): \CodeIgniter\HTTP\Response
    {
        $rfiModel = new RFIModel();
        $rfi      = $rfiModel->find($id);
        if (!$rfi) return $this->response->setJSON(['success' => false]);

        $respModel = new RFIResponseModel();
        $respId    = $respModel->insert([
            'rfi_id'     => $id,
            'user_id'    => $this->currentUser['id'],
            'body'       => $this->request->getPost('body'),
            'is_official'=> $this->request->getPost('is_official') ? 1 : 0,
        ]);

        // If official response, mark RFI as answered
        if ($this->request->getPost('is_official') && $rfi['status'] !== 'closed') {
            $rfiModel->update($id, ['status' => 'answered', 'answered_at' => date('Y-m-d H:i:s')]);
        }

        $resp = $respModel->select('rfi_responses.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name')
            ->join('fs_users','fs_users.id = rfi_responses.user_id','left')
            ->find($respId);

        return $this->response->setJSON(['success' => true, 'response' => $resp]);
    }

    /**
     * POST /rfis/:id/status — quick status update
     */
    public function updateStatus(int $id): \CodeIgniter\HTTP\Response
    {
        $rfiModel = new RFIModel();
        $status   = $this->request->getPost('status');
        $allowed  = ['draft','submitted','under_review','answered','closed'];
        if (!in_array($status, $allowed)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid status']);
        }
        $rfiModel->update($id, ['status' => $status]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /rfis/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $rfiModel = new RFIModel();
        $rfi      = $rfiModel->find($id);
        $rfiModel->delete($id);
        return redirect()->to(site_url("projects/{$rfi['project_id']}?tab=rfis"))
            ->with('success', 'RFI deleted.');
    }

    /**
     * GET /rfis/:id/export — basic CSV export of RFI + responses
     */
    public function export(int $id): void
    {
        $rfi       = (new RFIModel())->find($id);
        $responses = (new RFIResponseModel())->forRFI($id);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $rfi['rfi_number'] . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Field','Value']);
        foreach (['rfi_number','title','description','discipline','status','priority','due_date','cost_impact','schedule_impact'] as $k) {
            fputcsv($out, [$k, $rfi[$k] ?? '']);
        }
        fputcsv($out, ['---','RESPONSES']);
        foreach ($responses as $r) {
            fputcsv($out, [$r['author_name'] ?? '', $r['body'], $r['is_official'] ? 'Official' : '']);
        }
        fclose($out);
        exit;
    }
}
