<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'start_date', 'end_date', 'location', 'client_id', 'created_by'];
    protected $useTimestamps = true;
}
