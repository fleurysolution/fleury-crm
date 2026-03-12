<?php

namespace App\Controllers;

use App\Models\SiteDiaryModel;
use App\Models\SiteDiaryItemModel;
use App\Models\ProjectModel;
use App\Models\AreaModel;

class SiteDiary extends BaseAppController
{
    /**
     * GET /projects/:id/site-diary — list of diary entries
     */
    public function index(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $entries = (new SiteDiaryModel())->forProject($projectId, 60);

        return $this->render('site_diary/index', [
            'project' => $project,
            'entries' => $entries,
        ]);
    }

    /**
     * GET /projects/:id/site-diary/create — new entry form (defaults to today)
     */
    public function create(int $projectId)
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $date     = $this->request->getGet('date') ?: date('Y-m-d');
        $existing = (new SiteDiaryModel())->forDate($projectId, $date);
        if ($existing) {
            return redirect()->to(site_url("projects/{$projectId}/site-diary/{$existing['id']}"));
        }

        $areas = (new AreaModel())->where('project_id', $projectId)->findAll();
        $boq   = (new \App\Models\BOQItemModel())->where('project_id', $projectId)->where('is_section', 0)->where('deleted_at IS NULL')->findAll();

        return $this->render('site_diary/create', [
            'project' => $project,
            'areas'   => $areas,
            'boq'     => $boq,
            'date'    => $date,
        ]);
    }

    /**
     * POST /projects/:id/site-diary — store new diary entry
     */
    public function store(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $diaryModel = new SiteDiaryModel();
        $entryDate  = $this->request->getPost('entry_date') ?: date('Y-m-d');

        // Guard against duplicate same-day entry
        $existing = $diaryModel->forDate($projectId, $entryDate);
        if ($existing) {
            return redirect()->to(site_url("projects/{$projectId}/site-diary/{$existing['id']}"))
                ->with('info', 'A diary entry for this date already exists. Editing it instead.');
        }

        $diaryId = $diaryModel->insert([
            'project_id'     => $projectId,
            'tenant_id'      => session('tenant_id'),
            'branch_id'      => session('branch_id'),
            'entry_date'     => $entryDate,
            'weather'        => $this->request->getPost('weather'),
            'temperature'    => $this->request->getPost('temperature'),
            'manpower_count' => (int)$this->request->getPost('manpower_count'),
            'working_hours'  => (float)$this->request->getPost('working_hours'),
            'notes'          => $this->request->getPost('notes'),
            'status'         => 'draft',
            'created_by'     => $this->currentUser['id'],
        ]);

        // Save line items
        $this->saveLineItems($diaryId);

        return redirect()->to(site_url("projects/{$projectId}/site-diary/{$diaryId}"))
            ->with('success', 'Site diary entry saved for ' . date('d M Y', strtotime($entryDate)));
    }

    /**
     * GET /projects/:id/site-diary/:diaryId — view/edit entry
     */
    public function show(int $projectId, int $diaryId): string
    {
        $diary = (new SiteDiaryModel())->find($diaryId);
        if (!$diary || $diary['project_id'] != $projectId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $project = (new ProjectModel())->find($projectId);
        $items   = (new SiteDiaryItemModel())->forDiary($diaryId);
        $areas   = (new AreaModel())->where('project_id', $projectId)->findAll();
        $boq     = (new \App\Models\BOQItemModel())->where('project_id', $projectId)->where('is_section', 0)->where('deleted_at IS NULL')->findAll();

        return $this->render('site_diary/show', [
            'project' => $project,
            'diary'   => $diary,
            'items'   => $items,
            'areas'   => $areas,
            'boq'     => $boq,
        ]);
    }

    /**
     * POST /projects/:id/site-diary/:diaryId/update — save changes
     */
    public function update(int $projectId, int $diaryId): \CodeIgniter\HTTP\Response
    {
        $diaryModel = new SiteDiaryModel();
        $diaryModel->update($diaryId, [
            'weather'        => $this->request->getPost('weather'),
            'temperature'    => $this->request->getPost('temperature'),
            'manpower_count' => (int)$this->request->getPost('manpower_count'),
            'working_hours'  => (float)$this->request->getPost('working_hours'),
            'notes'          => $this->request->getPost('notes'),
        ]);

        // Rebuild line items
        (new SiteDiaryItemModel())->where('diary_id', $diaryId)->delete();
        $this->saveLineItems($diaryId);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /projects/:id/site-diary/:diaryId/submit
     */
    public function submit(int $projectId, int $diaryId): \CodeIgniter\HTTP\RedirectResponse
    {
        $sdModel = new SiteDiaryModel();
        $diary = $sdModel->find($diaryId);
        
        if (!$diary) {
            return redirect()->back()->with('error', 'Diary not found.');
        }

        $sdModel->update($diaryId, ['status' => 'Submitted']);

        // Trigger Workflow
        $workflow = new \App\Services\WorkflowEngine();
        $reqId = $workflow->submitRequest('site_diaries', 'project_site_diary', $diaryId, $this->currentUser['id'], [], session('branch_id'));
        
        if (!$reqId) {
            $sdModel->update($diaryId, [
                'status'      => 'Approved',
                'approved_by' => $this->currentUser['id'],
                'approved_at' => date('Y-m-d H:i:s')
            ]);
            return redirect()->back()->with('success', 'Diary approved automatically (no workflow defined).');
        }

        return redirect()->back()->with('success', 'Diary submitted for approval.');
    }

    /**
     * POST /projects/:id/site-diary/:diaryId/approve
     */
    public function approve(int $projectId, int $diaryId): \CodeIgniter\HTTP\RedirectResponse
    {
        $sdModel = new SiteDiaryModel();
        $diary = $sdModel->find($diaryId);
        if (!$diary) return redirect()->back()->with('error', 'Diary not found.');

        // Check if there is a pending workflow request
        $db = \Config\Database::connect();
        $req = $db->table('fs_approval_requests')
                  ->where('module_key', 'site_diaries')
                  ->where('entity_id', $diaryId)
                  ->where('status', 'pending')
                  ->get()->getRowArray();

        if ($req) {
            $workflow = new \App\Services\WorkflowEngine();
            $workflow->processAction($req['id'], $this->currentUser['id'], 'approved', 'Manual approval from diary view.');
        } else {
            $sdModel->update($diaryId, [
                'status'      => 'Approved',
                'approved_by' => $this->currentUser['id'],
                'approved_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // --- NEW: Update BOQ Production Quantities ---
        $this->updateBoqProduction($diaryId);

        return redirect()->back()->with('success', 'Diary approved and production quantities updated.');
    }

    /**
     * Update BOQ actual quantities based on approved diary line items.
     */
    protected function updateBoqProduction(int $diaryId): void
    {
        $items = (new SiteDiaryItemModel())->where('diary_id', $diaryId)->findAll();
        $boqModel = new \App\Models\BOQItemModel();

        foreach ($items as $item) {
            if ($item['boq_item_id'] && $item['quantity_done'] > 0) {
                $boqItem = $boqModel->find($item['boq_item_id']);
                if ($boqItem) {
                    $newQty = (float)$boqItem['actual_qty'] + (float)$item['quantity_done'];
                    $boqModel->update($item['boq_item_id'], [
                        'actual_qty'    => $newQty,
                        'actual_amount' => $newQty * (float)$boqItem['unit_rate']
                    ]);
                }
            }
        }
    }

    /**
     * Private: persist line items from POST data
     */
    private function saveLineItems(int $diaryId): void
    {
        $itemModel = new SiteDiaryItemModel();
        $types     = $this->request->getPost('item_type')        ?? [];
        $descs     = $this->request->getPost('item_description') ?? [];
        $areaIds   = $this->request->getPost('item_area_id')     ?? [];
        $boqIds    = $this->request->getPost('item_boq_item_id')  ?? [];
        $qtyDone   = $this->request->getPost('item_quantity_done')?? [];

        foreach ($descs as $i => $desc) {
            if (empty(trim((string)$desc))) continue;
            $itemModel->insert([
                'diary_id'      => $diaryId,
                'type'          => $types[$i]    ?? 'progress',
                'description'   => $desc,
                'area_id'       => ($areaIds[$i] ?? null) ?: null,
                'boq_item_id'   => ($boqIds[$i]  ?? null) ?: null,
                'quantity_done' => ($qtyDone[$i] ?? 0),
                'sort_order'    => $i,
            ]);
        }
    }
}
