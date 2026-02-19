<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateRequestModel extends Model
{
    protected $table = 'estimate_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'status'];
    protected $useTimestamps = true;
}
