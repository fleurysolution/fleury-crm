<?php

namespace App\Models;

class Material_categories_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'material_categories';
        parent::__construct($this->table);
    }
	function get_specific_category($options = array()) {
		$item_categories_table = $this->db->prefixTable('material_categories');
        $where = "";
		 $area_id = $this->_get_clean_value($options, "area_id");
		 if ($area_id) {
		   $where = " AND area_id=$area_id";
        }
		 $sql = "SELECT * FROM $item_categories_table WHERE deleted=0 $where";
         return $this->db->query($sql);
		 
	 }
	
	

    function get_details($options = array()) {
        $item_categories_table = $this->db->prefixTable('material_categories');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND c1.id=$id";
        }

        /*$sql = "SELECT $item_categories_table.*
        FROM $item_categories_table
        WHERE $item_categories_table.deleted=0 $where";*/
        $sql = "SELECT 
                c1.*,
                c2.title AS parent_category_name
            FROM $item_categories_table AS c1
            LEFT JOIN $item_categories_table AS c2 ON c1.parent_id = c2.id
            WHERE c1.deleted=0 $where";
        return $this->db->query($sql);
    }


    function get_categoriesByAreaId($options = array()){
        $item_area_table = $this->db->prefixTable('material_categories');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND id=$id";
        }
        $area_id = $this->_get_clean_value($options, "area_id");
        if ($area_id) {
            $where = " AND area_id=$area_id";
        }

        $sql = "SELECT * FROM $item_area_table WHERE deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_area_details($options = array()){
        $item_area_table = $this->db->prefixTable('area');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND id=$id";
        }

        $sql = "SELECT * FROM $item_area_table WHERE deleted=0 $where";
        return $this->db->query($sql);
    }

    function save_area($data){
        $item_area_table = $this->db->prefixTable('area');
        $where = "";
          if ($item_area_table->insert($data)) {
            return $item_area_table->getInsertID(); 
        }
         return false;

    }
}
