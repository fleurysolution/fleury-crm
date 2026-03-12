<?php namespace App\Controllers;

use App\Models\ProjectStaffingModel;
use App\Models\ProjectEquipmentModel;

class ProjectResources extends BaseAppController
{
    protected ProjectStaffingModel $manpower;
    protected ProjectEquipmentModel $equipment;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->manpower  = new ProjectStaffingModel();
        $this->equipment = new ProjectEquipmentModel();
    }

    public function storeStaffing(int $projectId)
    {
        $id = $this->manpower->insert([
            'project_id'     => $projectId,
            'role_or_trade'  => $this->request->getPost('role_or_trade'),
            'planned_count'  => $this->request->getPost('planned_count') ?? 0,
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'description'    => $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    public function updateStaffing(int $id)
    {
        $this->manpower->update($id, [
            'role_or_trade'  => $this->request->getPost('role_or_trade'),
            'planned_count'  => $this->request->getPost('planned_count'),
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'description'    => $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function deleteStaffing(int $id)
    {
        $this->manpower->delete($id);
        return $this->response->setJSON(['success' => true]);
    }

    public function storeEquipment(int $projectId)
    {
        $id = $this->equipment->insert([
            'project_id'     => $projectId,
            'equipment_type' => $this->request->getPost('equipment_type'),
            'planned_count'  => $this->request->getPost('planned_count') ?? 0,
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'description'    => $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    public function updateEquipment(int $id)
    {
        $this->equipment->update($id, [
            'equipment_type' => $this->request->getPost('equipment_type'),
            'planned_count'  => $this->request->getPost('planned_count'),
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'description'    => $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function deleteEquipment(int $id)
    {
        $this->equipment->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
