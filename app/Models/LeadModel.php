<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table          = 'leads';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields  = [
        'title',
        'type',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'vat_number',
        'gst_number',
        'currency',
        'currency_symbol',
        'labels',
        'status',
        'source',
        'value',
        'assigned_to',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    public function getLeadsByStage()
    {
        $leads = $this->findAll();
        $stages = [
            'new' => [],
            'contacted' => [],
            'qualified' => [],
            'proposal' => [],
            'won' => [],
            'lost' => []
        ];

        foreach ($leads as $lead) {
            if (isset($stages[$lead['status']])) {
                $stages[$lead['status']][] = $lead;
            }
        }

        return $stages;
    }
}
