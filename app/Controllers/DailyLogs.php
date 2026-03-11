<?php

namespace App\Controllers;

use App\Models\DailyLogModel;
use App\Models\DailyManpowerModel;
use App\Models\DailyEquipmentModel;
use App\Models\ProjectModel;

class DailyLogs extends BaseAppController
{
    protected DailyLogModel $logs;
    protected DailyManpowerModel $manpower;
    protected DailyEquipmentModel $equipment;
    protected ProjectModel $projects;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->logs      = new DailyLogModel();
        $this->manpower  = new DailyManpowerModel();
        $this->equipment = new DailyEquipmentModel();
        $this->projects  = new ProjectModel();
    }

    public function index(int $projectId)
    {
        // Simple JSON index for testing
        $logs = $this->logs->where('project_id', $projectId)->orderBy('date', 'DESC')->findAll();
        return $this->response->setJSON(['logs' => $logs]);
    }

    public function store(int $projectId)
    {
        // ErpModel expects explicit branch_id assignment to prevent silent linkage loss
        $data = [
            'tenant_id'          => session('tenant_id'),
            'branch_id'          => session('branch_id'),
            'project_id'         => $projectId,
            'date'               => $this->request->getPost('date') ?? date('Y-m-d'),
            'weather_conditions' => $this->request->getPost('weather_conditions'),
            'temperature'        => $this->request->getPost('temperature'),
            'site_conditions'    => $this->request->getPost('site_conditions'),
            'notes'              => $this->request->getPost('notes'),
            'status'             => 'draft',
            'created_by'         => session('user_id'),
        ];

        try {
            $id = $this->logs->insert($data);
            return $this->response->setJSON(['success' => true, 'log_id' => $id]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()])->setStatusCode(400);
        }
    }

    public function show(int $logId)
    {
        $log = $this->logs->find($logId);
        if (!$log) return $this->response->setStatusCode(404);

        $manpower  = $this->manpower->where('log_id', $logId)->findAll();
        $equipment = $this->equipment->where('log_id', $logId)->findAll();

        return $this->response->setJSON([
            'log'       => $log,
            'manpower'  => $manpower,
            'equipment' => $equipment
        ]);
    }

    public function addManpower(int $logId)
    {
        $id = $this->manpower->insert([
            'log_id'              => $logId,
            'trade_or_contractor' => $this->request->getPost('trade_or_contractor'),
            'worker_count'        => (int)$this->request->getPost('worker_count'),
            'hours'               => (float)$this->request->getPost('hours'),
            'notes'               => $this->request->getPost('notes'),
            'created_at'          => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    public function addEquipment(int $logId)
    {
        $id = $this->equipment->insert([
            'log_id'         => $logId,
            'equipment_type' => $this->request->getPost('equipment_type'),
            'hours_used'     => (float)$this->request->getPost('hours_used'),
            'status'         => $this->request->getPost('status'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }
}
