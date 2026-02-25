<?php

namespace App\Controllers;

use App\Models\TimesheetModel;
use App\Models\TimesheetEntryModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\CostCodeModel;
use App\Models\AreaModel;

class Timesheets extends BaseAppController
{
    /**
     * GET /timesheets — My timesheets list
     */
    public function index(): string
    {
        $tsModel = new TimesheetModel();
        $userId  = $this->currentUser['id'];

        $timesheets = $tsModel->withUserName()
            ->where('timesheets.user_id', $userId)
            ->orderBy('week_start', 'DESC')
            ->findAll();

        // Attach total hours per timesheet
        foreach ($timesheets as &$ts) {
            $ts['total_hours'] = $tsModel->totalHours($ts['id']);
        }
        unset($ts);

        return $this->render('timesheets/index', [
            'timesheets'   => $timesheets,
            'currentUser'  => $this->currentUser,
        ]);
    }

    /**
     * GET /timesheets/all — All timesheets (PM/Finance/Admin role)
     */
    public function all(): string
    {
        $tsModel    = new TimesheetModel();
        $timesheets = $tsModel->withUserName()
            ->orderBy('timesheets.week_start', 'DESC')
            ->orderBy('timesheets.status')
            ->paginate(50);

        foreach ($timesheets as &$ts) {
            $ts['total_hours'] = $tsModel->totalHours($ts['id']);
        }
        unset($ts);

        return $this->render('timesheets/all', [
            'timesheets'  => $timesheets,
            'pager'       => $tsModel->pager,
        ]);
    }

    /**
     * GET /timesheets/create — New timesheet form (pick week)
     */
    public function create(): string
    {
        $projectModel = new ProjectModel();
        $projects = $projectModel->where('status', 'active')->findAll();

        return $this->render('timesheets/create', [
            'projects' => $projects,
        ]);
    }

    /**
     * POST /timesheets/store — Create timesheet + entries
     */
    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tsModel  = new TimesheetModel();
        $entModel = new TimesheetEntryModel();
        $userId   = $this->currentUser['id'];

        $weekStart = $this->request->getPost('week_start');
        // Normalise to Monday
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($weekStart)));

        // Check for duplicate
        $existing = $tsModel->where('user_id', $userId)->where('week_start', $weekStart)->first();
        if ($existing) {
            return redirect()->to(site_url("timesheets/{$existing['id']}"))
                ->with('info', 'Timesheet for this week already exists.');
        }

        $tsId = $tsModel->insert([
            'user_id'    => $userId,
            'week_start' => $weekStart,
            'status'     => 'draft',
        ]);

        // Pre-create 7 blank entry rows (Mon–Sun × each posted project)
        $projects = $this->request->getPost('project_ids') ?? [];
        foreach ($projects as $projectId) {
            for ($d = 0; $d < 7; $d++) {
                $entModel->insert([
                    'timesheet_id' => $tsId,
                    'project_id'   => $projectId,
                    'entry_date'   => date('Y-m-d', strtotime($weekStart . " +{$d} days")),
                    'hours'        => 0,
                    'is_billable'  => 1,
                ]);
            }
        }

        return redirect()->to(site_url("timesheets/{$tsId}"))
            ->with('success', 'Timesheet created for week of ' . date('d M Y', strtotime($weekStart)));
    }

    /**
     * GET /timesheets/:id — View / fill timesheet
     */
    public function show(int $id): string
    {
        $tsModel  = new TimesheetModel();
        $entModel = new TimesheetEntryModel();

        $ts = $tsModel->withUserName()->where('timesheets.id', $id)->first();
        if (!$ts) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $entries  = $entModel->forTimesheet($id);
        $projects = (new ProjectModel())->where('status','active')->findAll();
        $tasks    = $entries ? (new TaskModel())->whereIn('project_id', array_unique(array_column($entries,'project_id')))->findAll() : [];

        // Build week day map
        $days = [];
        for ($d = 0; $d < 7; $d++) {
            $days[] = date('Y-m-d', strtotime($ts['week_start'] . " +{$d} days"));
        }

        // Group entries by project_id then entry_date
        $grid = [];
        foreach ($entries as $e) {
            $grid[$e['project_id']][$e['entry_date']] = $e;
        }

        return $this->render('timesheets/show', [
            'ts'      => $ts,
            'entries' => $entries,
            'days'    => $days,
            'grid'    => $grid,
            'projects'=> $projects,
            'tasks'   => $tasks,
            'totalHours' => $tsModel->totalHours($id),
        ]);
    }

    /**
     * POST /timesheets/:id/save — Save grid values (AJAX)
     */
    public function save(int $id): \CodeIgniter\HTTP\Response
    {
        $entModel = new TimesheetEntryModel();
        $rows     = $this->request->getPost('entries') ?? [];

        foreach ($rows as $entryId => $data) {
            if (!is_numeric($entryId)) continue;
            $entModel->update((int)$entryId, [
                'hours'       => (float)($data['hours']       ?? 0),
                'description' => $data['description']          ?? '',
                'task_id'     => $data['task_id']              ?? null,
                'is_billable' => isset($data['is_billable'])   ? 1 : 0,
            ]);
        }

        // Also handle new entry rows posted
        $newRows = $this->request->getPost('new_entries') ?? [];
        foreach ($newRows as $row) {
            if (empty($row['project_id']) || empty($row['entry_date'])) continue;
            $entModel->insert([
                'timesheet_id' => $id,
                'project_id'   => $row['project_id'],
                'task_id'      => $row['task_id']  ?? null,
                'entry_date'   => $row['entry_date'],
                'hours'        => (float)($row['hours'] ?? 0),
                'description'  => $row['description'] ?? '',
                'is_billable'  => isset($row['is_billable']) ? 1 : 0,
            ]);
        }

        $tsModel = new TimesheetModel();
        return $this->response->setJSON([
            'success'     => true,
            'total_hours' => $tsModel->totalHours($id),
        ]);
    }

    /**
     * POST /timesheets/:id/submit — Submit for approval
     */
    public function submit(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $tsModel = new TimesheetModel();
        $ts = $tsModel->find($id);
        if (!$ts || $ts['user_id'] !== $this->currentUser['id']) {
            return redirect()->back()->with('error', 'Not authorised.');
        }
        $tsModel->update($id, ['status' => 'submitted', 'submitted_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url("timesheets/{$id}"))->with('success', 'Timesheet submitted for approval.');
    }

    /**
     * POST /timesheets/:id/approve — Approve (PM/Finance)
     */
    public function approve(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $tsModel = new TimesheetModel();
        $tsModel->update($id, [
            'status'      => 'approved',
            'approved_by' => $this->currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->back()->with('success', 'Timesheet approved.');
    }

    /**
     * POST /timesheets/:id/reject — Reject with reason
     */
    public function reject(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $tsModel = new TimesheetModel();
        $tsModel->update($id, [
            'status'          => 'rejected',
            'rejected_reason' => $this->request->getPost('reason') ?? '',
        ]);
        return redirect()->back()->with('warning', 'Timesheet returned for revision.');
    }

    /**
     * GET /timesheets/:id/export — Export to CSV
     */
    public function export(int $id): \CodeIgniter\HTTP\Response
    {
        $tsModel  = new TimesheetModel();
        $entModel = new TimesheetEntryModel();
        $ts       = $tsModel->find($id);
        $entries  = $entModel->forTimesheet($id);

        $filename = 'timesheet_' . str_replace('-', '', $ts['week_start']) . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Date','Project','Task','Hours','Billable','Description']);
        foreach ($entries as $e) {
            if ((float)$e['hours'] === 0.0) continue;
            fputcsv($out, [
                $e['entry_date'],
                $e['project_title'] ?? '',
                $e['task_title']    ?? '',
                $e['hours'],
                $e['is_billable'] ? 'Yes' : 'No',
                $e['description']   ?? '',
            ]);
        }
        fclose($out);
        exit;
    }
}
