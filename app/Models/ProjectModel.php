<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'client_id', 'description', 'start_date', 'deadline', 'price', 'status'];
    protected $useTimestamps = true;
}
