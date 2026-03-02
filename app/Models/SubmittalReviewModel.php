<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmittalReviewModel extends Model
{
    protected $table         = 'submittal_revisions';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    
    // We omit 'deleted_at' here intentionally unless the migration added it
    protected $allowedFields = [
        'submittal_id', 'revision_no', 'status', 'reviewer_id', 
        'reviewed_at', 'notes', 'filepath', 'created_at', 'updated_at'
    ];

    /**
     * Get all review rounds / revisions for a specific submittal
     */
    public function forSubmittal(int $submittalId): array
    {
        return $this->select('submittal_revisions.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS reviewer_name')
            ->join('fs_users', 'fs_users.id = submittal_revisions.reviewer_id', 'left')
            ->where('submittal_id', $submittalId)
            ->orderBy('revision_no', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
