<?php

namespace App\Models;

use CodeIgniter\Model;

class RFIResponseModel extends Model
{
    protected $table         = 'rfi_responses';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['rfi_id','user_id','body','attachments','is_official'];

    public function forRFI(int $rfiId): array
    {
        return $this->select('rfi_responses.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name')
            ->join('fs_users', 'fs_users.id = rfi_responses.user_id', 'left')
            ->where('rfi_id', $rfiId)
            ->orderBy('created_at')
            ->findAll();
    }
}
