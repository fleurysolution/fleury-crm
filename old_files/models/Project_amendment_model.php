<?php

namespace App\Models;

class project_amendment_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'project_amendments';
        parent::__construct($this->table);
    }

    function save_amendment($data = array(), $id = 0) {
        $user_id = $this->_get_clean_value($data, "user_id");
        $project_id = $this->_get_clean_value($data, "project_id");
        if (!$project_id) {
            return false;
        }
            return parent::ci_save($data, $id);
      
    }

    function delete($id = 0, $undo = false) {
        return parent::delete($id, $undo);
    }

    function get_details($options = array()) {
        $project_amendments_table = $this->db->prefixTable('project_amendments');       
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND id=$id";
        }
        $project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND project_id=$project_id";
        }
        $sql = "SELECT * FROM $project_amendments_table WHERE deleted=0 $where"; 
        return $this->db->query($sql);
    }

    function get_project_amendments_dropdown_list($project_id = 0, $user_ids = array(), $add_client_contacts = false, $show_active_users_only = false) {
        $project_amendments_table = $this->db->prefixTable('project_amendments');
        $users_table = $this->db->prefixTable('users');

        $project_id = $this->_get_clean_value($project_id);

        $where = " AND $project_amendments_table.project_id=$project_id";

        if (is_array($user_ids) && count($user_ids)) {
            $users_list = join(",", $user_ids);
            $users_list = $this->_get_clean_value($users_list);
            $where .= " AND $users_table.id IN($users_list)";
        }

        $user_where = "";
        if (!$add_client_contacts) {
            $user_where .= " AND $users_table.user_type='staff'";
        }

        if ($show_active_users_only) {
            $user_where .= " AND $users_table.status='active'";
        }

        if ($user_where) {
            $where .= " AND $project_amendments_table.user_id IN (SELECT $users_table.id FROM $users_table WHERE $users_table.deleted=0 $user_where)";
        }

        $sql = "SELECT $project_amendments_table.user_id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS member_name, $users_table.status AS member_status, $users_table.user_type
        FROM $project_amendments_table
        LEFT JOIN $users_table ON $users_table.id= $project_amendments_table.user_id
        WHERE $project_amendments_table.deleted=0 $where 
        GROUP BY $project_amendments_table.user_id 
        ORDER BY $users_table.user_type, $users_table.first_name ASC";
        return $this->db->query($sql);
    }

    function is_user_a_project_member($project_id = 0, $user_id = 0) {
        $info = $this->get_one_where(array("project_id" => $project_id, "user_id" => $user_id, "deleted" => 0));
        if ($info->id) {
            return true;
        }
    }

    function get_rest_team_amendment_for_a_project($project_id = 0) {
        $project_amendments_table = $this->db->prefixTable('project_amendments');
        $users_table = $this->db->prefixTable('users');
        $project_id = $this->_get_clean_value($project_id);

        $sql = "SELECT $users_table.id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS member_name
        FROM $users_table
        LEFT JOIN $project_amendments_table ON $project_amendments_table.user_id=$users_table.id
        WHERE $users_table.user_type='staff' AND $users_table.status='active' AND $users_table.deleted=0 AND $users_table.id NOT IN (SELECT $project_amendments_table.user_id FROM $project_amendments_table WHERE $project_amendments_table.project_id='$project_id' AND deleted=0)
        ORDER BY $users_table.first_name ASC";

        return $this->db->query($sql);
    }

    function get_client_contacts_of_the_project_client($project_id = 0) {
        $project_amendments_table = $this->db->prefixTable('project_amendments');
        $users_table = $this->db->prefixTable('users');
        $projects_table = $this->db->prefixTable('projects');
        $project_id = $this->_get_clean_value($project_id);

        $sql = "SELECT $users_table.id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS contact_name
        FROM $users_table
        LEFT JOIN $project_amendments_table ON $project_amendments_table.user_id=$users_table.id
        WHERE $users_table.user_type='client' AND $users_table.deleted=0 AND $users_table.client_id=(SELECT $projects_table.client_id FROM $projects_table WHERE $projects_table.id=$project_id) AND $users_table.id NOT IN (SELECT $project_amendments_table.user_id FROM $project_amendments_table WHERE $project_amendments_table.project_id='$project_id' AND deleted=0)
        ORDER BY $users_table.first_name ASC";

        return $this->db->query($sql);
    }

    function project_amended_price($project_id){
        $project_amendments_table = $this->db->prefixTable('project_amendments');
        $projects_table = $this->db->prefixTable('projects');
        $sql="UPDATE $projects_table SET price = (SELECT SUM(amended_price) FROM $project_amendments_table WHERE project_id = '$project_id' AND DELETED = 0 ) WHERE id = '$project_id'";
       return $result=$this->db->query($sql);
    }

}
