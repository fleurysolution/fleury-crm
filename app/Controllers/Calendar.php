<?php

namespace App\Controllers;

use App\Models\CalendarEventModel;
use App\Models\ActivityLogModel;

class Calendar extends BaseAppController
{
    /**
     * GET /calendar — main calendar page
     */
    public function index(): string
    {
        $db       = \Config\Database::connect();
        $projects = $db->table('projects')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('title', 'ASC')->get()->getResultArray();

        return $this->render('calendar/index', ['projects' => $projects]);
    }

    /**
     * GET /calendar/events?start=...&end=...&project_id=...
     * JSON feed for FullCalendar (unified: events + tasks + milestones)
     */
    public function events(): \CodeIgniter\HTTP\Response
    {
        $start     = $this->request->getGet('start') ?? date('Y-m-01');
        $end       = $this->request->getGet('end')   ?? date('Y-m-t');
        $projectId = (int)($this->request->getGet('project_id') ?? 0) ?: null;

        $items = (new CalendarEventModel())->unifiedFeed($start, $end, $projectId);
        return $this->response->setJSON($items);
    }

    /**
     * POST /calendar/events — create a new calendar event
     */
    public function store(): \CodeIgniter\HTTP\Response
    {
        $m  = new CalendarEventModel();
        $id = $m->insert([
            'project_id'  => $this->request->getPost('project_id') ?: null,
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type') ?: 'other',
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'all_day'     => $this->request->getPost('all_day') ? 1 : 0,
            'location'    => $this->request->getPost('location'),
            'color'       => $this->request->getPost('color') ?: '#3b82f6',
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'created_by'  => $this->currentUser['id'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        ActivityLogModel::log('calendar_event', $m->db->insertID(), 'created',
            "Event created: {$this->request->getPost('title')}");

        return $this->response->setJSON(['success' => true, 'id' => $m->db->insertID()]);
    }

    /**
     * POST /calendar/events/:id/update — update event
     */
    public function update(int $id): \CodeIgniter\HTTP\Response
    {
        $m = new CalendarEventModel();
        $m->update($id, [
            'project_id'  => $this->request->getPost('project_id') ?: null,
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type') ?: 'other',
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'all_day'     => $this->request->getPost('all_day') ? 1 : 0,
            'location'    => $this->request->getPost('location'),
            'color'       => $this->request->getPost('color') ?: '#3b82f6',
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /calendar/events/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\Response
    {
        (new CalendarEventModel())->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /calendar/events/:id/drag — drag-and-drop reschedule (FullCalendar eventDrop)
     */
    public function drag(int $id): \CodeIgniter\HTTP\Response
    {
        $m = new CalendarEventModel();
        $e = $m->find($id);
        if (!$e) return $this->response->setJSON(['success' => false]);

        $m->update($id, [
            'start_date' => $this->request->getPost('start_date'),
            'end_date'   => $this->request->getPost('end_date') ?: null,
            'all_day'    => $this->request->getPost('all_day') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }
}
