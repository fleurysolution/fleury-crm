<?php

namespace App\Models;

class Service_assignment_state_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'service_assignment_state';
        parent::__construct($this->table);
    }

    function get_last_assigned($service_id) {
        $state = $this->db->prefixTable('service_assignment_state');
        $service_id = (int)$service_id;

        $row = $this->db->query("SELECT * FROM $state WHERE service_id=$service_id LIMIT 1")->getRow();
        return $row ? (int)($row->last_assigned_team_member_id ?? 0) : 0;
    }

    function set_last_assigned($service_id, $team_member_id) {
        $state = $this->db->prefixTable('service_assignment_state');
        $service_id = (int)$service_id;
        $team_member_id = (int)$team_member_id;

        $exists = $this->db->query("SELECT id FROM $state WHERE service_id=$service_id LIMIT 1")->getRow();

        if ($exists && !empty($exists->id)) {
            $this->db->query("UPDATE $state SET last_assigned_team_member_id=$team_member_id WHERE service_id=$service_id");
        } else {
            $this->db->query("INSERT INTO $state (service_id, last_assigned_team_member_id) VALUES ($service_id, $team_member_id)");
        }

        return true;
    }
}
