<?php

namespace App\Models;

use CodeIgniter\Model;

class RFIResponseModel extends Model
{
    protected $table         = 'fs_rfi_replies';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['rfi_id','user_id','reply','is_official_answer'];

    public function forRFI(int $rfiId): array
    {
        return $this->select('rfi_responses.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name')
            ->join('fs_users', 'fs_users.id = rfi_responses.user_id', 'left')
            ->where('rfi_id', $rfiId)
            ->orderBy('created_at')
            ->findAll();
    }
}
