<?php

namespace App\Models;

use CodeIgniter\Model;

class ProposalModel extends Model
{
    protected $table = 'proposals';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'proposal_date', 'valid_until', 'status', 'content'];
    protected $useTimestamps = true;
}
