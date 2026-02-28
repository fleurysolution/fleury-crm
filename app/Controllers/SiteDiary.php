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

        return $this->render('site_diary/create', [
            'project' => $project,
            'areas'   => $areas,
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

        return $this->render('site_diary/show', [
            'project' => $project,
            'diary'   => $diary,
            'items'   => $items,
            'areas'   => $areas,
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
        (new SiteDiaryModel())->update($diaryId, ['status' => 'submitted']);
        return redirect()->back()->with('success', 'Diary submitted for approval.');
    }

    /**
     * POST /projects/:id/site-diary/:diaryId/approve
     */
    public function approve(int $projectId, int $diaryId): \CodeIgniter\HTTP\RedirectResponse
    {
        (new SiteDiaryModel())->update($diaryId, [
            'status'      => 'approved',
            'approved_by' => $this->currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->back()->with('success', 'Diary approved.');
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

        foreach ($descs as $i => $desc) {
            if (empty(trim((string)$desc))) continue;
            $itemModel->insert([
                'diary_id'    => $diaryId,
                'type'        => $types[$i]    ?? 'progress',
                'description' => $desc,
                'area_id'     => ($areaIds[$i] ?? null) ?: null,
                'sort_order'  => $i,
            ]);
        }
    }
}
