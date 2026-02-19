<?php

namespace App\Models;

class Service_team_members_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'service_team_members';
        parent::__construct($this->table);
    }

    /**
     * Returns eligible staff for a service (active mapping + active staff users)
     */
    function get_eligible_members($service_id) {
        $map = $this->db->prefixTable('service_team_members');
        $users = $this->db->prefixTable('users');

        $service_id = (int) $service_id;

        $sql = "SELECT 
                    $users.id,
                    $users.first_name,
                    $users.last_name,
                    $users.email,
                    $users.status,
                    $users.disable_login,
                    $map.priority
                FROM $map
                INNER JOIN $users ON $users.id = $map.team_member_id
                WHERE $map.service_id = $service_id
                  AND $map.is_active = 1
                  AND $users.deleted = 0
                  AND $users.user_type = 'staff'
                  AND $users.status = 'active'
                  AND $users.disable_login = 0
                ORDER BY 
                    CASE WHEN $map.priority IS NULL THEN 999999 ELSE $map.priority END ASC,
                    $users.id ASC";

        return $this->db->query($sql);
    }

    /**
     * Get mapped member ids (for pre-checking in UI)
     */
    function get_mapped_member_ids($service_id) {
        $map = $this->db->prefixTable('service_team_members');
        $service_id = (int) $service_id;

        $sql = "SELECT team_member_id FROM $map
                WHERE service_id=$service_id AND is_active=1";
        $rows = $this->db->query($sql)->getResult();

        return array_map(function($r){ return (int)$r->team_member_id; }, $rows);
    }

    /**
     * Replace mappings for a service (atomic-ish via soft delete + insert)
     */
    function replace_mappings($service_id, $member_ids = array()) {
        $map = $this->db->prefixTable('service_team_members');
        $service_id = (int) $service_id;

        // Deactivate all existing
        $this->db->query("UPDATE $map SET is_active=0 WHERE service_id=$service_id");

        // Insert / reactivate selected
        foreach ($member_ids as $member_id) {
            $member_id = (int) $member_id;

            // Try reactivate if exists
            $sqlCheck = "SELECT id FROM $map WHERE service_id=$service_id AND team_member_id=$member_id LIMIT 1";
            $existing = $this->db->query($sqlCheck)->getRow();

            if ($existing && !empty($existing->id)) {
                $this->db->query("UPDATE $map SET is_active=1 WHERE id=" . (int)$existing->id);
            } else {
                $this->db->query("INSERT INTO $map (service_id, team_member_id, is_active, created_at)
                                  VALUES ($service_id, $member_id, 1, NOW())");
            }
        }

        return true;
    }


     public function assign_staff($appointment_id, $staff_id, $assigned_by) {
            $appointments = $this->db->prefixTable('customer_appointments');

            $appointment_id = (int)$appointment_id;
            $staff_id = (int)$staff_id;
            $assigned_by = (int)$assigned_by;

            $sql = "UPDATE $appointments
                    SET staff_id=$staff_id,
                        assignment_status='assigned',
                        assigned_at=NOW(),
                        assigned_by=$assigned_by
                    WHERE id=$appointment_id AND deleted=0";

            return $this->db->query($sql);
        }

        public function get_one_active($appointment_id) {
            $appointments = $this->db->prefixTable('customer_appointments');
            $appointment_id = (int)$appointment_id;

            $sql = "SELECT * FROM $appointments WHERE id=$appointment_id AND deleted=0 LIMIT 1";
            return $this->db->query($sql)->getRow();
        }

        public function modal_service_team_members() {
            $this->access_only_team_members();

            $this->validate_submitted_data([
                "service_id" => "required|numeric"
            ]);

            $service_id = $this->request->getPost('service_id');

            // Fetch all active staff
            $users_table = $this->db->prefixTable('users');
            $staff = $this->db->query("
                SELECT id, first_name, last_name, email
                FROM $users_table
                WHERE deleted=0
                  AND user_type='staff'
                  AND status='active'
                  AND disable_login=0
                ORDER BY first_name ASC, last_name ASC
            ")->getResult();

            $mapped_ids = $this->Service_team_members_model->get_mapped_member_ids($service_id);

            return $this->template->view('appointment_services/service-team-members-modal', [
                "service_id" => $service_id,
                "staff_list" => $staff,
                "mapped_ids" => $mapped_ids
            ]);
        }
}
