<?php

namespace App\Models;

use CodeIgniter\Model;

class MeetingAttendeeModel extends Model
{
    protected $table          = 'meeting_attendees';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['meeting_id', 'user_id', 'status'];

    public function getForMeeting(int $meetingId)
    {
        return $this->select('meeting_attendees.*, CONCAT(first_name, " ", last_name) as name')
                    ->join('users', 'users.id = meeting_attendees.user_id')
                    ->where('meeting_id', $meetingId)
                    ->findAll();
    }
}
