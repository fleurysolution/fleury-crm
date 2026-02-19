<?php

namespace App\Models;

class Materials_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'materials';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $items_table = $this->db->prefixTable('materials');
        $order_items_table = $this->db->prefixTable('order_materials');
        $item_categories_table = $this->db->prefixTable('material_categories');
		$areas_table = $this->db->prefixTable('areas');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $items_table.id=$id";
        }

        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($items_table.title LIKE '%$search%' ESCAPE '!' OR $items_table.description LIKE '%$search%' ESCAPE '!')";
        }

        $show_in_client_portal = $this->_get_clean_value($options, "show_in_client_portal");
        if ($show_in_client_portal) {
            $where .= " AND $items_table.show_in_client_portal=1";
        }

        $category_id = $this->_get_clean_value($options, "category_id");
        if ($category_id) {
            $where .= " AND $items_table.category_id=$category_id";
        }
		$project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $items_table.project_id=$project_id";
        }
		$area_id = $this->_get_clean_value($options, "area_id");
		if ($area_id) {
            $where .= " AND $items_table.area_id=$area_id";
        }
		
		
		

        $extra_select = "";
        $login_user_id = $this->_get_clean_value($options, "login_user_id");
        $created_by_hash = $this->_get_clean_value($options, "created_by_hash");
        if ($login_user_id || $created_by_hash) {

            $extra_where = "";
            if ($login_user_id) {
                $extra_where = " AND $order_items_table.created_by=$login_user_id ";
            } else if ($created_by_hash) {
                $extra_where = " AND $order_items_table.created_by_hash='$created_by_hash' ";
            }

            if ($login_user_id && $created_by_hash) {
                $extra_where = " AND ($order_items_table.created_by=$login_user_id OR $order_items_table.created_by_hash='$created_by_hash') ";
            }

            $extra_select = ", (SELECT COUNT($order_items_table.id) FROM $order_items_table WHERE $order_items_table.deleted=0 AND $order_items_table.order_id=0 AND $order_items_table.item_id=$items_table.id $extra_where ) AS added_to_cart";
        }

        $limit_query = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $offset = $this->_get_clean_value($options, "offset");
            $limit_query = "LIMIT $offset, $limit";
        }

        $sql = "SELECT $items_table.*, $item_categories_table.title as category_title,$areas_table.areaname as area_name $extra_select
        FROM $items_table   
        LEFT JOIN $item_categories_table ON $item_categories_table.id= $items_table.category_id
		LEFT JOIN $areas_table ON $areas_table.id= $items_table.area_id
        WHERE $items_table.deleted=0 $where
        ORDER BY $items_table.title ASC 
        $limit_query";
        
        return $this->db->query($sql);
    }

}
