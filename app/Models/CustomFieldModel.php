<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFieldModel extends Model
{
    protected $table          = 'custom_fields';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = [
        'tenant_id',
        'object_type', // 'projects', 'clients', or custom_object_id
        'field_name',
        'field_label',
        'field_type', // 'text', 'number', 'date', 'select'
        'options',    // JSON for select types
        'is_required',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function forObject(string $objectType, ?int $tenantId)
    {
        return $this->where('object_type', $objectType)
                    ->where('tenant_id', $tenantId)
                    ->findAll();
    }
}
