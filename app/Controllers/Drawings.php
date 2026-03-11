<?php

namespace App\Controllers;

use App\Models\DrawingModel;
use App\Models\DrawingRevisionModel;
use App\Models\DrawingPinModel;
use App\Models\ProjectModel;

class Drawings extends BaseAppController
{
    protected $drawings;
    protected $revisions;
    protected $pins;

    public function __construct()
    {
        $this->drawings  = new DrawingModel();
        $this->revisions = new DrawingRevisionModel();
        $this->pins      = new DrawingPinModel();
    }

    public function store(int $projectId)
    {
        $project = (new ProjectModel())->find($projectId);
        
        $data = [
            'tenant_id'      => $project['tenant_id'],
            'branch_id'      => $project['branch_id'],
            'project_id'     => $projectId,
            'discipline'     => $this->request->getPost('discipline'),
            'drawing_number' => $this->request->getPost('drawing_no') ?: $this->request->getPost('drawing_number'),
            'title'          => $this->request->getPost('title'),
            'status'         => 'active'
        ];

        $drawingId = $this->drawings->insert($data);

        // Upload file and create revision
        $file = $this->request->getFile('drawing_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/drawings', $newName);
            
            $revId = $this->revisions->insert([
                'drawing_id'    => $drawingId,
                'revision_no'   => $this->request->getPost('initial_revision') ?: '01',
                'revision_date' => $this->request->getPost('revision_date') ?: date('Y-m-d'),
                'notes'         => $this->request->getPost('notes'),
                'filepath'      => $newName,
                'uploaded_by'   => session()->get('user_id')
            ]);

            $this->drawings->update($drawingId, ['current_revision_id' => $revId]);
        }

        return redirect()->to(site_url("projects/{$projectId}?tab=drawings"))->with('message', 'Drawing uploaded.');
    }

    public function view(int $drawingId)
    {
        $drawing = $this->drawings->find($drawingId);
        if (!$drawing) return redirect()->back()->with('error', 'Drawing not found.');

        $currentRev = $this->revisions->find($drawing['current_revision_id']);
        $pins = $this->pins->getForDrawing($drawingId);

        $data = [
            'title'      => $drawing['drawing_number'] . ' - ' . $drawing['title'],
            'drawing'    => $drawing,
            'revision'   => $currentRev,
            'pins'       => $pins
        ];

        return $this->render('drawings/view', $data);
    }

    public function addPin(int $drawingId)
    {
        $data = [
            'drawing_id'  => $drawingId,
            'revision_id' => $this->request->getPost('revision_id'),
            'pos_x'       => $this->request->getPost('pos_x'),
            'pos_y'       => $this->request->getPost('pos_y'),
            'pin_type'    => $this->request->getPost('pin_type'),
            'content'     => $this->request->getPost('content'),
            'created_by'  => session()->get('user_id')
        ];

        $this->pins->insert($data);
        return $this->response->setJSON(['success' => true]);
    }

    public function uploadRevision(int $drawingId)
    {
        $drawing = $this->drawings->find($drawingId);
        if (!$drawing) return $this->response->setJSON(['success' => false, 'error' => 'Drawing not found.'])->setStatusCode(404);

        $file = $this->request->getFile('drawing_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/drawings', $newName);
            
            $revId = $this->revisions->insert([
                'drawing_id'    => $drawingId,
                'revision_no'   => $this->request->getPost('revision_no'),
                'revision_date' => $this->request->getPost('revision_date') ?: date('Y-m-d'),
                'notes'         => $this->request->getPost('notes'),
                'filepath'      => $newName,
                'uploaded_by'   => session()->get('user_id')
            ]);

            $this->drawings->update($drawingId, ['current_revision_id' => $revId]);
            return $this->response->setJSON(['success' => true, 'revision_id' => $revId]);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'Invalid file.'])->setStatusCode(400);
    }

    public function delete(int $drawingId)
    {
        $drawing = $this->drawings->find($drawingId);
        if (!$drawing) return $this->response->setJSON(['success' => false, 'error' => 'Drawing not found.'])->setStatusCode(404);

        $this->drawings->delete($drawingId);
        return $this->response->setJSON(['success' => true]);
    }
}
