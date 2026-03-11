<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFieldValueModel extends Model
{
    protected $table          = 'custom_field_values';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['record_id', 'field_id', 'value'];
}
