<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractModel extends Model
{
    protected $table = 'contracts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'title', 'contract_date', 'valid_until', 'status', 'content'];
    protected $useTimestamps = true;
}
