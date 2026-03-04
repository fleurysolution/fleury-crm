<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorApplicationModel extends Model
{
    protected $table            = 'vendor_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'trade_type',
        'tax_id',
        'w9_path',
        'insurance_path',
        'status',
        'user_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
