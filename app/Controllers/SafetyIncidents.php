<?php

namespace App\Controllers;

use App\Models\SafetyIncidentModel;
use App\Models\ProjectModel;

class SafetyIncidents extends BaseAppController
{
    /**
     * GET /projects/:id/safety
     */
    public function index(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $safetyModel = new SafetyIncidentModel();
        $incidents = $safetyModel->forProject($projectId);

        return $this->render('safety/index', [
            'project'   => $project,
            'incidents' => $incidents,
        ]);
    }

    /**
     * POST /projects/:id/safety
     */
    public function store(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $safetyModel = new SafetyIncidentModel();
        
        $data = [
            'tenant_id'     => session('tenant_id'),
            'branch_id'     => session('branch_id'),
            'project_id'    => $projectId,
            'incident_date' => $this->request->getPost('incident_date') ?: date('Y-m-d H:i:s'),
            'type'          => $this->request->getPost('type'),
            'severity'      => $this->request->getPost('severity'),
            'description'   => $this->request->getPost('description'),
            'reported_by'   => $this->currentUser['id'],
            'status'        => 'open',
            'created_by'    => $this->currentUser['id'],
        ];

        // Ensure data isolation
        $safetyModel->insert($data);
        
        return redirect()->to("projects/{$projectId}/safety")->with('success', 'Safety Incident reported successfully.');
    }

    /**
     * POST /projects/:id/safety/:id/status
     */
    public function updateStatus(int $projectId, int $incidentId): \CodeIgniter\HTTP\RedirectResponse
    {
        $safetyModel = new SafetyIncidentModel();
        $incident = $safetyModel->find($incidentId);
        
        if ($incident && $incident['project_id'] == $projectId) {
            $safetyModel->update($incidentId, [
                'status' => $this->request->getPost('status')
            ]);
        }
        
        return redirect()->to("projects/{$projectId}/safety")->with('success', 'Incident status updated.');
    }

    /**
     * POST /projects/:id/safety/:id/delete
     */
    public function delete(int $projectId, int $incidentId): \CodeIgniter\HTTP\RedirectResponse
    {
        $safetyModel = new SafetyIncidentModel();
        $incident = $safetyModel->find($incidentId);
        
        if ($incident && $incident['project_id'] == $projectId) {
            $safetyModel->delete($incidentId);
        }
        
        return redirect()->to("projects/{$projectId}/safety")->with('success', 'Incident deleted.');
    }
}
