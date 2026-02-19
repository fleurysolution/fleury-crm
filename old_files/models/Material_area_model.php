<?php

namespace App\Models;

class Material_area_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'areas';
        parent::__construct($this->table);
    }

    
    function get_details($options = array()){
        $item_area_table = $this->db->prefixTable('areas');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND id=$id";
        }
         $project_id=$this->_get_clean_value($options, "project_id");
		
        if ($project_id) {
            $where = " AND project_id=$project_id";
        }
        $sql = "SELECT * FROM $item_area_table WHERE deleted=0 $where";
        return $this->db->query($sql);
    }

    function save_area($data){
        
          if ($this->insert($data)) {
            return $this->getInsertID(); 
        }
         return false;

    }
}
