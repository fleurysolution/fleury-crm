<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'client_id', 'lead_id', 'project_id', 'assigned_to', 'status', 'start_date', 'deadline', 'created_by'];
    protected $useTimestamps = true;
}
