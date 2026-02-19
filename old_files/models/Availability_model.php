<?php

namespace App\Models;

class Availability_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'team_availability';
        parent::__construct($this->table);
    }

    function get_team_member_availability($user_id){
        $team_availability=$this->db->prefixTable('team_availability');
        $where = ""; 
        if ($user_id) {
            $where = " AND $team_availability.team_member_id=$user_id";
        }

        $sql = "SELECT $team_availability.* FROM $team_availability WHERE 0=0 $where"; //die;
        return $this->db->query($sql);
    }

    function get_details($options = array()) {
        $team_table = $this->db->prefixTable('team');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $team_table.id=$id";
        }

        $sql = "SELECT $team_table.*
        FROM $team_table
        WHERE $team_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_members($team_ids = array()) {
        $team_table = $this->db->prefixTable('team');
        $team_ids = implode(",", $team_ids);
        $team_ids = $this->_get_clean_value($team_ids);

        $sql = "SELECT $team_table.members
        FROM $team_table
        WHERE $team_table.deleted=0 AND id in($team_ids)";
        return $this->db->query($sql);
    }

    function get_id_and_title($options = array()) {
        $team_table = $this->db->prefixTable('team');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $team_table.id=$id";
        }

        $sql = "SELECT $team_table.id, $team_table.title
        FROM $team_table
        WHERE $team_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    

}
