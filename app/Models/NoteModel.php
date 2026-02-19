<?php

namespace App\Models;

use CodeIgniter\Model;

class NoteModel extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'client_id', 'lead_id', 'created_by', 'labels'];
    protected $useTimestamps = true;
}
