<?php

namespace App\Models;

use CodeIgniter\Model;

class PageModel extends Model
{
    protected $table         = 'fs_pages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'slug','title','content','status',
        'internal_use_only','visible_to_team_members_only','visible_to_clients_only',
        'full_width','hide_topbar','deleted'
    ];

    public function findActiveBySlug(string $slug): ?array
    {
        return $this->where([
                'slug'    => $slug,
                'status'  => 'active',
                'deleted' => 0,
            ])->first();
    }
}