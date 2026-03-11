<?php

namespace App\Models;

class PurchaseOrderModel extends ErpModel
{
    protected $table          = 'project_purchase_orders';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'project_id',
        'tenant_id',
        'branch_id',
        'vendor_id',
        'po_number',
        'title',
        'status',
        'total_amount',
        'notes',
        'delivery_date',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all Purchase Orders for a specific project, including vendor details.
     */
    public function forProject(int $projectId): array
    {
        return $this->select('project_purchase_orders.*, CONCAT(vendor.first_name, " ", vendor.last_name) AS vendor_name, CONCAT(creator.first_name, " ", creator.last_name) AS creator_name')
            ->join('fs_users vendor', 'vendor.id = project_purchase_orders.vendor_id', 'left')
            ->join('fs_users creator', 'creator.id = project_purchase_orders.created_by', 'left')
            ->where('project_purchase_orders.project_id', $projectId)
            ->orderBy('project_purchase_orders.created_at', 'DESC')
            ->findAll();
    }
}
