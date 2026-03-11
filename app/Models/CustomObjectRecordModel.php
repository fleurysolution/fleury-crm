<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomObjectRecordModel extends Model
{
    protected $table          = 'custom_object_records';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['tenant_id', 'custom_object_id'];
    protected $useTimestamps   = true;
}
