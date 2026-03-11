<?php

namespace App\Controllers;

use App\Models\InspectionModel;
use App\Models\InspectionItemModel;
use App\Models\ProjectModel;

class Inspections extends BaseAppController
{
    /**
     * GET /projects/:id/inspections
     */
    public function index(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $inspModel = new InspectionModel();
        
        $inspections = $inspModel->forProject($projectId);

        return $this->render('inspections/index', [
            'project'     => $project,
            'inspections' => $inspections,
        ]);
    }

    /**
     * GET /projects/:id/inspections/:id
     */
    public function show(int $projectId, int $inspectionId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $inspModel = new InspectionModel();
        $inspection = $inspModel->find($inspectionId);
        
        if (!$inspection || $inspection['project_id'] != $projectId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = (new InspectionItemModel())->forInspection($inspectionId);

        return $this->render('inspections/show', [
            'project'    => $project,
            'inspection' => $inspection,
            'items'      => $items,
        ]);
    }

    /**
     * POST /projects/:id/inspections
     */
    public function store(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $inspModel = new InspectionModel();
        
        $data = [
            'tenant_id'       => session('tenant_id'),
            'branch_id'       => session('branch_id'),
            'project_id'      => $projectId,
            'type'            => $this->request->getPost('type'),
            'status'          => $this->request->getPost('status') ?: 'draft',
            'inspection_date' => $this->request->getPost('inspection_date') ?: date('Y-m-d'),
            'inspector_id'    => $this->request->getPost('inspector_id') ?: null,
            'notes'           => $this->request->getPost('notes'),
            'created_by'      => $this->currentUser['id'],
        ];

        // ErpModel's branch check requires branch_id to be present
        $inspModel->insert($data);
        
        return redirect()->to("projects/{$projectId}/inspections")->with('success', 'Inspection created successfully.');
    }

    /**
     * POST /projects/:id/inspections/:id/delete
     */
    public function delete(int $projectId, int $inspectionId): \CodeIgniter\HTTP\RedirectResponse
    {
        $inspModel = new InspectionModel();
        $inspection = $inspModel->find($inspectionId);
        if ($inspection && $inspection['project_id'] == $projectId) {
            $inspModel->delete($inspectionId);
        }
        return redirect()->to("projects/{$projectId}/inspections")->with('success', 'Inspection deleted.');
    }
}
