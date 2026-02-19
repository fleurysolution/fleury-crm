<?php

namespace App\Models;

class Package_price_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'package_price';
        parent::__construct($this->table);
    }
	function get_specific_category($options = array()) {
		$item_categories_table = $this->db->prefixTable('package_price');
        $where = "";
		 $area_id = $this->_get_clean_value($options, "area_id");
		 if ($area_id) {
		   $where = " AND area_id=$area_id";
        }
		 $sql = "SELECT * FROM $item_categories_table WHERE deleted=0 $where";
         return $this->db->query($sql);
		 
	 }
	 
	 function getPackageById($id){
	     $package_price_table = $this->db->prefixTable('package_price');
    
            // Build the base SQL query
            $sql = "SELECT * FROM $package_price_table WHERE id=$id";
            return $this->db->query($sql);
	     
	 }
        function getPackageByStripeId($stripeID) {
            $package_price_table = $this->db->prefixTable('package_price');
            $sql = "SELECT * FROM $package_price_table WHERE stripe_product_id = ?";
           return $query = $this->db->query($sql, [$stripeID]);
        }

     function getPackageBystripeProductId($stripeProductId){
         $package_price_table = $this->db->prefixTable('package_price');
    
            // Build the base SQL query
           echo $sql = "SELECT * FROM $package_price_table WHERE stripe_product_id=$stripeProductId"; //die;
            //return $this->db->query($sql);
         
     }
	
function get_active_package($options = array()) {
    $package_price_table = $this->db->prefixTable('package_price');
    
    // Build the base SQL query
    $sql = "SELECT * FROM $package_price_table WHERE status = 1 ORDER BY order_priority ASC";
    
    // Check for filters
    if (!empty($options['id'])) {
        $id = intval($options['id']);
        $sql .= " AND id = $id";
    }
    
    // Execute the query and return results
    return $this->db->query($sql);
}

	

  function get_details($options = array()) {
    $package_price_table = $this->db->prefixTable('package_price');
    
    // Build the base SQL query
    $sql = "SELECT * FROM $package_price_table WHERE 1=1";
    
    // Check for filters
    $id = isset($options['id']) ? intval($options['id']) : null;
    if ($id) {
        $sql .= " AND id = $id";
    }
    
    // Execute the query and return results
    return $this->db->query($sql);
}

function delete_package($id){
     $package_price_table = $this->db->prefixTable('package_price');
    
     $sql = "DELETE FROM $package_price_table WHERE id=$id";
      return $this->db->query($sql);
    
    
}


    function get_categoriesByAreaId($options = array()){
        $item_area_table = $this->db->prefixTable('package_price');
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
