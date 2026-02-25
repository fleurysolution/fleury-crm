<?php

namespace App\Controllers;

use App\Models\SubmittalModel;
use App\Models\SubmittalRevisionModel;
use App\Models\ProjectModel;

class Submittals extends BaseAppController
{
    /**
     * GET /projects/:id/submittals — submittal register
     */
    public function index(int $projectId): string
    {
        $project  = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $subModel = new SubmittalModel();
        $submittals = $subModel->forProject($projectId);
        $counts     = $subModel->statusCounts($projectId);

        return $this->render('submittals/index', [
            'project'    => $project,
            'submittals' => $submittals,
            'counts'     => $counts,
        ]);
    }

    /**
     * GET /submittals/:id — detail + revision trail
     */
    public function show(int $id): string
    {
        $subModel  = new SubmittalModel();
        $submittal = $subModel->withUserName()->find($id);
        if (!$submittal) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $project   = (new ProjectModel())->find($submittal['project_id']);
        $revisions = (new SubmittalRevisionModel())->forSubmittal($id);

        return $this->render('submittals/show', [
            'project'   => $project,
            'submittal' => $submittal,
            'revisions' => $revisions,
        ]);
    }

    /**
     * POST /projects/:id/submittals — create new submittal
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $subModel = new SubmittalModel();
        $data = [
            'project_id'       => $projectId,
            'submittal_number' => $subModel->nextNumber($projectId),
            'title'            => $this->request->getPost('title'),
            'spec_section'     => $this->request->getPost('spec_section'),
            'type'             => $this->request->getPost('type') ?: 'shop_drawing',
            'status'           => 'submitted',
            'submitted_by'     => $this->currentUser['id'],
            'reviewer_id'      => $this->request->getPost('reviewer_id') ?: null,
            'due_date'         => $this->request->getPost('due_date')    ?: null,
            'current_revision' => 0,
        ];

        $subId = $subModel->insert($data);

        // Create initial revision record
        (new SubmittalRevisionModel())->insert([
            'submittal_id' => $subId,
            'revision_no'  => 0,
            'status'       => 'submitted',
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'id' => $subId, 'number' => $data['submittal_number']]);
        }
        return redirect()->to(site_url("submittals/{$subId}"))->with('success', 'Submittal ' . $data['submittal_number'] . ' created.');
    }

    /**
     * POST /submittals/:id/review — add review decision + create new revision
     */
    public function review(int $id): \CodeIgniter\HTTP\Response
    {
        $subModel = new SubmittalModel();
        $sub      = $subModel->find($id);
        if (!$sub) return $this->response->setJSON(['success' => false]);

        $status  = $this->request->getPost('status');
        $notes   = $this->request->getPost('notes');
        $newRev  = (int)$sub['current_revision'] + 1;

        (new SubmittalRevisionModel())->insert([
            'submittal_id' => $id,
            'revision_no'  => $newRev,
            'status'       => $status,
            'reviewer_id'  => $this->currentUser['id'],
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'notes'        => $notes,
        ]);

        $subModel->update($id, [
            'status'           => $status,
            'current_revision' => $newRev,
            'reviewer_id'      => $this->currentUser['id'],
        ]);

        return $this->response->setJSON(['success' => true, 'status' => $status, 'revision' => $newRev]);
    }

    /**
     * POST /submittals/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $subModel = new SubmittalModel();
        $sub      = $subModel->find($id);
        $subModel->delete($id);
        return redirect()->to(site_url("projects/{$sub['project_id']}?tab=submittals"))
            ->with('success', 'Submittal deleted.');
    }
}
