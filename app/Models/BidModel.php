<?php

namespace App\Models;

use CodeIgniter\Model;

class BidModel extends Model
{
    protected $table          = 'bids';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['tenant_id', 'package_id', 'vendor_id', 'vendor_name', 'amount', 'notes', 'attachment_path', 'status'];
    protected $useTimestamps   = true;
    protected $updatedField    = ''; // Bids are usually not updated much after submission in this simple flow

    public function getForPackage(int $packageId)
    {
        return $this->where('package_id', $packageId)
                    ->orderBy('amount', 'ASC')
                    ->findAll();
    }
}
