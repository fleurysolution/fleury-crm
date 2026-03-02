<?php

namespace App\Controllers;

use App\Models\DrawingModel;
use App\Models\DrawingRevisionModel;
use App\Models\ProjectModel;

class Drawings extends BaseAppController
{
    /**
     * POST /projects/:id/drawings
     * Creates a new Master Drawing and its initial Rev 0.
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dModel = new DrawingModel();
        
        $drawingId = $dModel->insert([
            'project_id'       => $projectId,
            'drawing_no'       => $this->request->getPost('drawing_no'),
            'title'            => $this->request->getPost('title'),
            'discipline'       => $this->request->getPost('discipline'),
            'status'           => 'Current',
            'current_revision' => $this->request->getPost('initial_revision') ?: '00',
            'created_by'       => $this->currentUser['id'],
        ]);

        $revData = [
            'drawing_id'    => $drawingId,
            'revision_no'   => $this->request->getPost('initial_revision') ?: '00',
            'revision_date' => $this->request->getPost('revision_date') ?: date('Y-m-d'),
            'notes'         => $this->request->getPost('notes'),
            'uploaded_by'   => $this->currentUser['id'],
        ];

        // Handle File Upload
        $file = $this->request->getFile('drawing_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Storing drawing PDFs in a specialized folder inside uploads
            if (!is_dir(FCPATH . 'uploads/drawings')) {
                mkdir(FCPATH . 'uploads/drawings', 0777, true);
            }
            $file->move(FCPATH . 'uploads/drawings', $newName);
            $revData['filepath'] = 'drawings/' . $newName;
        }

        (new DrawingRevisionModel())->insert($revData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to(site_url("projects/{$projectId}?tab=drawings"))->with('success', 'Drawing added.');
    }

    /**
     * GET /drawings/:id
     * Detail view of a drawing with revision history.
     */
    public function show(int $id): string
    {
        $dModel = new DrawingModel();
        $drawing = $dModel->find($id);
        if (!$drawing) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $project = (new \App\Models\ProjectModel())->find($drawing['project_id']);
        $revisions = (new DrawingRevisionModel())->forDrawing($id);

        return $this->render('drawings/show', [
            'project'   => $project,
            'drawing'   => $drawing,
            'revisions' => $revisions,
        ]);
    }

    /**
     * POST /drawings/:id/revisions
     * Upload a new revision for an existing drawing.
     */
    public function uploadRevision(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dModel = new DrawingModel();
        $drawing = $dModel->find($id);
        if (!$drawing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Drawing not found.']);
        }

        $revNo = $this->request->getPost('revision_no');

        $revData = [
            'drawing_id'    => $id,
            'revision_no'   => $revNo,
            'revision_date' => $this->request->getPost('revision_date') ?: date('Y-m-d'),
            'notes'         => $this->request->getPost('notes'),
            'uploaded_by'   => $this->currentUser['id'],
        ];

        $file = $this->request->getFile('drawing_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            if (!is_dir(FCPATH . 'uploads/drawings')) {
                mkdir(FCPATH . 'uploads/drawings', 0777, true);
            }
            $file->move(FCPATH . 'uploads/drawings', $newName);
            $revData['filepath'] = 'drawings/' . $newName;
        }

        (new DrawingRevisionModel())->insert($revData);

        // Update Master Drawing definition to this new revision
        $dModel->update($id, [
            'current_revision' => $revNo,
            'status'           => 'Current'
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->back()->with('success', 'New Revision Uploaded.');
    }

    /**
     * POST /drawings/:id/delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $dModel = new DrawingModel();
        $drv    = $dModel->find($id);
        if ($drv) {
            $dModel->delete($id);
            return redirect()->to(site_url("projects/{$drv['project_id']}?tab=drawings"))
                ->with('success', 'Drawing deleted.');
        }
        return redirect()->back()->with('error', 'Drawing not found.');
    }
}
