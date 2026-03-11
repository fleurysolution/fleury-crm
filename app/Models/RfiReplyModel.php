<?php

namespace App\Models;

use CodeIgniter\Model;

class RfiReplyModel extends Model
{
    protected $table = 'fs_rfi_replies';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // Threads usually don't soft delete unless main RFI deletes, handled down stream

    protected $allowedFields = [
        'rfi_id',
        'user_id',
        'reply',
        'is_official_answer'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';
}
