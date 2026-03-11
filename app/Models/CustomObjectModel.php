<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomObjectModel extends Model
{
    protected $table          = 'custom_objects';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = [
        'tenant_id',
        'name',
        'label_singular',
        'label_plural',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function forTenant(?int $tenantId)
    {
        return $this->where('tenant_id', $tenantId)->findAll();
    }
}
