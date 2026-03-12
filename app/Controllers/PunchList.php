<?php

namespace App\Controllers;

use App\Models\PunchListItemModel;
use App\Models\ProjectModel;
use App\Models\AreaModel;

class PunchList extends BaseAppController
{
    /**
     * GET /projects/:id/punch-list — punch list for a project
     */
    public function index(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $plModel = new PunchListItemModel();
        $status  = $this->request->getGet('status') ?? '';

        $items   = $plModel->forProject($projectId, $status);
        $counts  = $plModel->statusCounts($projectId);
        $aging   = $plModel->agingCounts($projectId);
        $areas   = (new AreaModel())->where('project_id', $projectId)->findAll();

        return $this->render('punch_list/index', [
            'project' => $project,
            'items'   => $items,
            'counts'  => $counts,
            'aging'   => $aging,
            'areas'   => $areas,
            'filter'  => $status,
        ]);
    }

    /**
     * POST /projects/:id/punch-list — create item
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response
    {
        $plModel = new PunchListItemModel();
        $data = [
            'tenant_id'   => session('tenant_id'),
            'branch_id'   => session('branch_id'),
            'project_id'  => $projectId,
            'item_number' => $plModel->nextNumber($projectId),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'trade'       => $this->request->getPost('trade'),
            'priority'    => $this->request->getPost('priority') ?: 'medium',
            'status'      => 'open',
            'area_id'     => $this->request->getPost('area_id')     ?: null,
            'task_id'     => $this->request->getPost('task_id')     ?: null,
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'reported_by' => $this->currentUser['id'],
            'due_date'    => $this->request->getPost('due_date')    ?: null,
            'latitude'    => $this->request->getPost('latitude')    ?: null,
            'longitude'   => $this->request->getPost('longitude')   ?: null,
        ];

        // Handle Photo Upload
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $dir = FCPATH . 'uploads/projects/' . $projectId . '/punch-list/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file->move($dir, $newName);
            $data['photo_path'] = 'uploads/projects/' . $projectId . '/punch-list/' . $newName;
        }

        $id = $plModel->insert($data);
        $item = $plModel->forProject($projectId);
        $item = array_values(array_filter($item, fn($i) => $i['id'] === $id))[0] ?? $plModel->find($id);

        return $this->response->setJSON(['success' => true, 'id' => $id, 'item' => $item]);
    }

    /**
     * POST /punch-list/:id/resolve — mark resolved
     */
    public function resolve(int $id): \CodeIgniter\HTTP\Response
    {
        $plModel = new PunchListItemModel();
        $plModel->update($id, ['status' => 'resolved', 'resolved_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /punch-list/:id/close — mark closed
     */
    public function close(int $id): \CodeIgniter\HTTP\Response
    {
        $plModel = new PunchListItemModel();
        $plModel->update($id, ['status' => 'closed', 'closed_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /punch-list/:id/status — arbitrary status update
     */
    public function updateStatus(int $id): \CodeIgniter\HTTP\Response
    {
        $plModel = new PunchListItemModel();
        $status  = $this->request->getPost('status');
        $allowed = ['open','in_progress','resolved','closed','voided'];
        if (!in_array($status, $allowed)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid status']);
        }
        $update = ['status' => $status];
        if ($status === 'resolved') $update['resolved_at'] = date('Y-m-d H:i:s');
        if ($status === 'closed')   $update['closed_at']   = date('Y-m-d H:i:s');
        $plModel->update($id, $update);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /punch-list/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\Response
    {
        (new PunchListItemModel())->delete($id);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * GET /projects/:id/punch-list/export — CSV export
     */
    public function exportCsv(int $projectId): void
    {
        $items    = (new PunchListItemModel())->forProject($projectId);
        $project  = (new ProjectModel())->find($projectId);
        $filename = 'punch-list-' . preg_replace('/\s+/', '-', $project['title'] ?? $projectId) . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['#','Title','Trade','Area','Status','Priority','Assigned','Due Date','Days Open']);
        foreach ($items as $it) {
            $daysOpen = $it['created_at']
                ? (int)((time() - strtotime($it['created_at'])) / 86400) : 0;
            fputcsv($out, [
                $it['item_number'],
                $it['title'],
                $it['trade']         ?? '',
                $it['area_name']     ?? '',
                $it['status'],
                $it['priority'],
                $it['assignee_name'] ?? '',
                $it['due_date']      ?? '',
                in_array($it['status'],['open','in_progress']) ? $daysOpen : '',
            ]);
        }
        fclose($out);
        exit;
    }
}
