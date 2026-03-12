<?php

namespace App\Controllers;

use App\Models\MeetingModel;
use App\Models\MeetingAttendeeModel;
use App\Models\ProjectModel;

class Meetings extends BaseAppController
{
    protected $meetings;
    protected $attendees;
    protected $projects;

    public function __construct()
    {
        $this->meetings  = new MeetingModel();
        $this->attendees = new MeetingAttendeeModel();
        $this->projects  = new ProjectModel();
    }

    public function store(int $projectId)
    {
        $project   = $this->projects->find($projectId);
        $tenantId  = session()->get('tenant_id') ?: ($project['tenant_id'] ?? null);
        
        $data = [
            'tenant_id'    => $tenantId,
            'project_id'   => $projectId,
            'title'        => $this->request->getPost('title'),
            'meeting_date' => $this->request->getPost('meeting_date'),
            'meeting_time' => $this->request->getPost('meeting_time'),
            'location'     => $this->request->getPost('location'),
            'agenda'       => $this->request->getPost('agenda'),
            'status'       => 'scheduled'
        ];

        $meetingId = $this->meetings->insert($data);

        // Add creator as attendee
        $this->attendees->insert([
            'meeting_id' => $meetingId,
            'user_id'    => session()->get('user_id'),
            'status'     => 'present'
        ]);

        return redirect()->to(site_url("projects/{$projectId}?tab=meetings"))->with('message', 'Meeting scheduled.');
    }

    public function updateMinutes(int $meetingId)
    {
        $tenantId = session()->get('tenant_id');
        $minutes = $this->request->getPost('minutes');
        
        $this->meetings->where('tenant_id', $tenantId)->update($meetingId, [
            'minutes' => $minutes,
            'status'  => 'completed'
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function updateAttendee(int $meetingId)
    {
        $userId = $this->request->getPost('user_id');
        $status = $this->request->getPost('status');

        $exists = $this->attendees->where('meeting_id', $meetingId)->where('user_id', $userId)->first();
        if ($exists) {
            $this->attendees->update($exists['id'], ['status' => $status]);
        } else {
            $this->attendees->insert([
                'meeting_id' => $meetingId,
                'user_id'    => $userId,
                'status'     => $status
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }
}
