<?php

namespace App\Controllers;

use App\Controllers\BaseAppController;
use App\Models\AreaModel;

class Areas extends BaseAppController
{
    protected AreaModel $areas;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->areas = new AreaModel();
    }

    public function index(int $projectId): string
    {
        return view('projects/tabs/areas_inline', [
            'project' => (new ProjectModel())->find($projectId),
        ]);
    }

    public function store(int $projectId)
    {
        $id = $this->areas->insert([
            'project_id'   => $projectId,
            'parent_id'    => $this->request->getPost('parent_id') ?: null,
            'name'         => $this->request->getPost('name'),
            'type'         => $this->request->getPost('type') ?? 'other',
            'status'       => $this->request->getPost('status') ?? 'planning',
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'turnover_date'=> $this->request->getPost('turnover_date') ?: null,
            'description'  => $this->request->getPost('description') ?: null,
            'sort_order'   => (int)$this->request->getPost('sort_order'),
        ]);
        return $this->response->setJSON(['success' => true, 'id' => $id, 'tree' => $this->areas->getTree($projectId)]);
    }

    public function update(int $id)
    {
        $this->areas->update($id, [
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'status'        => $this->request->getPost('status'),
            'start_date'    => $this->request->getPost('start_date') ?: null,
            'end_date'      => $this->request->getPost('end_date') ?: null,
            'turnover_date' => $this->request->getPost('turnover_date') ?: null,
            'parent_id'     => $this->request->getPost('parent_id') ?: null,
            'description'   => $this->request->getPost('description') ?: null,
        ]);
        $area = $this->areas->find($id);
        return $this->response->setJSON(['success' => true, 'tree' => $this->areas->getTree($area['project_id'])]);
    }

    public function delete(int $id)
    {
        $area = $this->areas->find($id);
        $this->areas->delete($id);
        return $this->response->setJSON(['success' => true, 'tree' => $this->areas->getTree($area['project_id'])]);
    }
}
