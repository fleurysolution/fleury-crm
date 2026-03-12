<?php

namespace App\Models;

class AssetRegistryModel extends ErpModel
{
    protected $table          = 'project_asset_registry';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = false;
    protected $allowedFields  = [
        'project_id', 'item_name', 'category', 'serial_number', 
        'manufacturer', 'warranty_expiry', 'installation_date', 
        'manual_url', 'submittal_id', 'qr_code_token'
    ];

    public function forProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)->findAll();
    }
}
