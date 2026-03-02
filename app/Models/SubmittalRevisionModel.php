<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmittalRevisionModel extends Model
{
    protected $table         = 'submittal_revisions';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'submittal_id','revision_no','status','reviewer_id','reviewed_at','notes','filepath',
        'signed_at', 'signature_ip', 'signature_data'
    ];

    public function forSubmittal(int $submittalId): array
    {
        return $this->select('submittal_revisions.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS reviewer_name')
            ->join('fs_users', 'fs_users.id = submittal_revisions.reviewer_id', 'left')
            ->where('submittal_id', $submittalId)
            ->orderBy('revision_no', 'DESC')
            ->findAll();
    }
}
