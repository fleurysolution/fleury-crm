<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectProgressPhotoModel extends Model
{
    protected $table = 'project_progress_photos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'project_id',
        'photo_path',
        'caption',
        'uploaded_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
