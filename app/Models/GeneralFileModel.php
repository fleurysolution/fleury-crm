<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralFileModel extends Model
{
    protected $table = 'general_files';
    protected $primaryKey = 'id';
    protected $allowedFields = ['file_name', 'file_size', 'client_id', 'lead_id', 'description', 'uploaded_by', 'sys_file_name'];
    protected $useTimestamps = true;
}
