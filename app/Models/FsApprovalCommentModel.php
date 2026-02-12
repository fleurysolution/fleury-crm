<?php

namespace App\Models;

use CodeIgniter\Model;

class FsAsApprovalCommentModel extends Model
{
    protected $table         = 'fs_as_approval_comments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'approval_request_id',
        'commented_by',
        'comment',
        'created_at',
    ];

    public $useTimestamps = false;
}
