<?php

namespace App\Models;

use CodeIgniter\Model;

class PoItemModel extends Model
{
    protected $table          = 'project_po_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // standard item association

    protected $allowedFields = [
        'po_id',
        'cost_code_id',
        'task_id',
        'area_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'total'
    ];

    /**
     * Get all line items for a specific Purchase Order.
     */
    public function forPo(int $poId): array
    {
        return $this->where('po_id', $poId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
