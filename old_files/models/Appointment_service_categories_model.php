<?php

namespace App\Models;

class Appointment_service_categories_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'service_categories';
        parent::__construct($this->table);
    }
	function get_specific_category($options = array()) {
		$service_categories = $this->db->prefixTable('service_categories');
        $where = "";
		
		 $sql = "SELECT * FROM $service_categories WHERE deleted=0 $where";
         return $this->db->query($sql);
		 
	 }
	
	

    /*function get_details($options = array()) {
        $item_categories_table = $this->db->prefixTable('service_categories');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND id=$id";
        }

        $sql = "SELECT $item_categories_table.*
        FROM $item_categories_table
        WHERE $item_categories_table.deleted=0 $where";
        
        return $this->db->query($sql);
    }*/
    function get_details($options = array()) {
        $categories = $this->db->prefixTable('service_categories');

        $where = " AND $categories.deleted=0";

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $categories.id=$id";
        }

        // Filter only active categories (useful in public booking)
        $only_active = get_array_value($options, "only_active");
        if ($only_active) {
            $where .= " AND $categories.is_active=1";
        }

        // Admin filter: active/inactive
        $is_active = get_array_value($options, "is_active");
        if ($is_active === "0" || $is_active === "1") {
            $where .= " AND $categories.is_active=" . ((int)$is_active);
        }

        $sql = "SELECT $categories.*
                FROM $categories
                WHERE 1=1 $where
                ORDER BY $categories.display_order ASC, $categories.name ASC";

        return $this->db->query($sql);
    }
}
