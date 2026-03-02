<?php

namespace App\Models;

use CodeIgniter\Model;

class SovItemModel extends Model
{
    protected $table          = 'project_sov_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // standard relational

    protected $allowedFields = [
        'project_id',
        'item_no',
        'description',
        'scheduled_value',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all SOV items for a specific project.
     */
    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
