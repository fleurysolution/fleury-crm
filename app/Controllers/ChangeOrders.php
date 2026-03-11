<?php

namespace App\Controllers;

use App\Models\ChangeEventModel;
use App\Models\ChangeOrderModel;
use App\Models\ProjectModel;
use App\Services\AutomationService;

class ChangeOrders extends BaseAppController
{
    protected $events;
    protected $orders;
    protected $projects;
    protected $automation;

    public function __construct()
    {
        $this->events = new ChangeEventModel();
        $this->orders = new ChangeOrderModel();
        $this->projects = new ProjectModel();
        $this->automation = new AutomationService();
    }

    public function storeEvent(int $projectId)
    {
        $tenantId = session()->get('tenant_id');
        
        $data = [
            'tenant_id'      => $tenantId,
            'project_id'     => $projectId,
            'title'          => $this->request->getPost('title'),
            'description'    => $this->request->getPost('description'),
            'type'           => $this->request->getPost('type'),
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'status'         => 'potential'
        ];

        $eventId = $this->events->insert($data);
        $this->automation->trigger('change_orders', 'create', array_merge(['id' => $eventId], $data), $tenantId);

        return redirect()->to(site_url("projects/{$projectId}?tab=change_management"))->with('message', 'Change event created.');
    }

    public function updateEventStatus(int $eventId)
    {
        $tenantId = session()->get('tenant_id');
        $status = $this->request->getPost('status');
        
        $this->events->where('tenant_id', $tenantId)->update($eventId, ['status' => $status]);
        
        return $this->response->setJSON(['success' => true]);
    }

    public function convertToCO(int $eventId)
    {
        $tenantId = session()->get('tenant_id');
        $event = $this->events->where('tenant_id', $tenantId)->find($eventId);
        
        if (!$event) return redirect()->back()->with('error', 'Event not found.');

        // Generate CO Number
        $projectCOs = $this->orders->where('project_id', $event['project_id'])->countAllResults();
        $coNumber = 'CO-' . str_pad($projectCOs + 1, 3, '0', STR_PAD_LEFT);

        $data = [
            'tenant_id'   => $tenantId,
            'project_id'  => $event['project_id'],
            'event_id'    => $eventId,
            'co_number'   => $coNumber,
            'title'       => $event['title'],
            'description' => $event['description'],
            'amount'      => $event['estimated_cost'],
            'status'      => 'draft'
        ];

        $this->orders->insert($data);
        $this->events->update($eventId, ['status' => 'approved']);

        return redirect()->to(site_url("projects/{$event['project_id']}?tab=change_management"))->with('message', 'Converted to Change Order.');
    }

    public function approveCO(int $coId)
    {
        $tenantId = session()->get('tenant_id');
        $updateData = [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ];
        $this->orders->where('tenant_id', $tenantId)->update($coId, $updateData);
        
        $co = $this->orders->find($coId);
        $this->automation->trigger('change_orders', 'update', $co, $tenantId);

        return $this->response->setJSON(['success' => true]);
    }
}
