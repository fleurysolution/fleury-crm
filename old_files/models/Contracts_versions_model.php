<?php

namespace App\Models;

class Contracts_versions_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'pcm_contracts_versions';
        parent::__construct($this->table);
    }

  

      public function createVersion(array $versionData)
    {
        
        if ($this->insert($versionData)) {
            return $this->insertID();  // Return the ID of the inserted version
        }
        
        return false; 
    }

    /**
     * Get the last version number for a contract
     *
     * @param int $contractId
     * @return string|null Last version number or null if not found
     */
    public function getLastVersionNumber($contractId)
    {
        return $this->where('contract_id', $contractId)
                    ->orderBy('version_number', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRow('version_number');
    }

}
