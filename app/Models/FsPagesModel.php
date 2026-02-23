<?php

namespace App\Models;

use CodeIgniter\Model;

class FsPagesModel extends Model
{
    protected $table            = 'fs_pages';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'status',
        'internal_only',
        'visible_to',
        'layout_full_width',
        'hide_topbar',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getActiveBySlug(string $slug)
    {
        return $this->where('slug', $slug)
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->first();
    }
}