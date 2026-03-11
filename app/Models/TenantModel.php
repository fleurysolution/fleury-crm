<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantModel extends Model
{
    protected $table          = 'tenants';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'name',
        'industry',
        'employee_count',
        'country',
        'currency',
        'timezone',
        'status',
        'stripe_customer_id',
        'package_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function get_details($id = null)
    {
        $builder = $this->db->table($this->table);
        if ($id) {
            return $builder->where('id', $id)->get()->getRow();
        }
        return $builder->where('deleted_at', null)->get()->getResult();
    }
}
