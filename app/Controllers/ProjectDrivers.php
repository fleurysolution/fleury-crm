<?php namespace App\Controllers;

use App\Models\ProjectDriverModel;
use App\Models\ProjectModel;

class ProjectDrivers extends BaseAppController
{
    protected ProjectDriverModel $drivers;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->drivers = new ProjectDriverModel();
    }

    public function index(int $projectId): string
    {
        return view('projects/tabs/drivers_inline', [
            'project' => (new ProjectModel())->find($projectId),
        ]);
    }

    public function store(int $projectId)
    {
        $id = $this->drivers->insert([
            'project_id' => $projectId,
            'name'       => $this->request->getPost('name'),
            'unit'       => $this->request->getPost('unit'),
            'value'      => $this->request->getPost('value') ?? 0,
            'description'=> $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    public function update(int $id)
    {
        $this->drivers->update($id, [
            'name'       => $this->request->getPost('name'),
            'unit'       => $this->request->getPost('unit'),
            'value'      => $this->request->getPost('value'),
            'description'=> $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete(int $id)
    {
        $this->drivers->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
