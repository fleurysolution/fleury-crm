<?php

namespace App\Controllers;

use App\Models\ProcurementModel;
use App\Models\BidComparisonModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Helpers\CPMHelper;

class Procurement extends ErpController
{
    public function index(int $projectId)
    {
        $projectModel = new ProjectModel();
        $procurementModel = new ProcurementModel();
        $taskModel = new TaskModel();

        $data['project'] = $projectModel->find($projectId);
        $data['items'] = $procurementModel->where('project_id', $projectId)->findAll();
        $data['tasks'] = $taskModel->where('project_id', $projectId)->findAll();

        return view('projects/procurement/index', $data);
    }

    public function saveItem(int $projectId)
    {
        $procurementModel = new ProcurementModel();
        
        $id = $this->request->getPost('id');
        $data = [
            'project_id'       => $projectId,
            'task_id'          => $this->request->getPost('task_id'),
            'item_name'        => $this->request->getPost('item_name'),
            'vendor_id'        => $this->request->getPost('vendor_id'),
            'lead_time_days'   => $this->request->getPost('lead_time_days'),
            'expected_on_site' => $this->request->getPost('expected_on_site'),
            'status'           => $this->request->getPost('status'),
            'notes'            => $this->request->getPost('notes'),
        ];

        if ($id) {
            $procurementModel->update($id, $data);
        } else {
            $procurementModel->insert($data);
        }

        // Trigger schedule recalculation
        CPMHelper::recalculate($projectId);

        return $this->response->setJSON(['success' => true]);
    }

    public function bidLeveling(int $projectId)
    {
        $projectModel = new ProjectModel();
        $bidModel = new BidComparisonModel();
        
        $data['project'] = $projectModel->find($projectId);
        // Simplified bid leveling logic for demonstration:
        // In a real system, we'd join with BOQ items and Vendors
        $data['bids'] = $bidModel->where('project_id', $projectId)->findAll();

        return view('projects/procurement/bid_leveling', $data);
    }
}
