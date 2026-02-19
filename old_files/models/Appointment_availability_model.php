<?php

namespace App\Models;

class Appointment_availability_model extends Crud_model {

    protected $table = 'weekly_availability';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'staff_id', 'day_of_week', 'start_time', 'end_time',
        'break_start_time', 'break_end_time',
        'duration', 'is_available', 'deleted', 'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    function __construct() {
        parent::__construct($this->table);
    }

    public function softDeleteByStaff(int $staff_id, bool $undo = false): bool
    {
        validate_numeric_value($staff_id);

        $data = ['deleted' => $undo ? 0 : 1];

        return (bool) $this->db->table($this->table)
            ->where('staff_id', $staff_id)
            ->update($data);
    }

    public function upsertDay(array $data): bool
    {
        // Required: staff_id + day_of_week
        if (empty($data['staff_id']) || empty($data['day_of_week'])) {
            return false;
        }

        // If a record exists for this staff/day (even deleted), update it; else insert.
        $builder = $this->db->table($this->table);
        $existing = $builder->select('id')
            ->where('staff_id', $data['staff_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        if ($existing && isset($existing->id)) {
            $builder = $this->db->table($this->table);
            return (bool) $builder->where('id', $existing->id)->update($data);
        }

        $builder = $this->db->table($this->table);
        return (bool) $builder->insert($data);
    }

    function get_details($options = array()) {
        $weekly_availability = $this->db->prefixTable('weekly_availability');
        $where = " AND $weekly_availability.deleted=0";

        $staff_id = $this->_get_clean_value($options, "staff_id");
        if ($staff_id) {
            $where .= " AND $weekly_availability.staff_id=$staff_id";
        }

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $weekly_availability.id=$id";
        }

        $sql = "SELECT * FROM $weekly_availability WHERE 1=1 $where";
        return $this->db->query($sql);
    }

    public function getDayAvailability(int $staff_id, string $day_short)
        {
            $table = $this->db->prefixTable('weekly_availability');

            $sql = "SELECT *
                    FROM $table
                    WHERE deleted = 0
                      AND staff_id = ?
                      AND day_of_week = ?
                    LIMIT 1";

            return $this->db->query($sql, [$staff_id, $day_short])->getRow();
        }

        public function findFirstAvailableStaffForDay(string $day_short): ?int
        {
            $table = $this->db->prefixTable('weekly_availability');

            $sql = "SELECT staff_id
                    FROM $table
                    WHERE deleted = 0
                      AND is_available = 1
                      AND day_of_week = ?
                    ORDER BY staff_id ASC
                    LIMIT 1";

            $row = $this->db->query($sql, [$day_short])->getRow();
            return $row ? (int)$row->staff_id : null;
        }

        /*function deleteOldAvailability($staff_id, $undo = false) {
                validate_numeric_value($staff_id);

                $weekly = $this->db->prefixTable('weekly_availability');
                $flag = ($undo === true) ? 0 : 1;

                $sql = "UPDATE $weekly SET deleted=$flag WHERE staff_id=" . (int)$staff_id;
                return $this->db->query($sql);
            }*/

            function deleteOldAvailability($staff_id) {
                validate_numeric_value($staff_id);
                $table = $this->db->prefixTable('weekly_availability');

                return $this->db->query("DELETE FROM $table WHERE staff_id=" . (int)$staff_id);
            }

            public function get_active_staff_weekly_windows_for_day(string $dayShort): array
                {
                    $weekly = $this->db->prefixTable('weekly_availability');
                    $users  = $this->db->prefixTable('users');

                    $dayShort = $this->_get_clean_value($dayShort);

                    $sql = "SELECT wa.staff_id,
                                   wa.day_of_week,
                                   wa.start_time,
                                   wa.end_time,
                                   wa.is_available,
                                   u.first_name,
                                   u.last_name
                            FROM $weekly wa
                            JOIN $users u ON u.id = wa.staff_id
                            WHERE wa.deleted=0
                              AND wa.is_available=1
                              AND wa.day_of_week='$dayShort'
                              AND u.deleted=0
                              AND u.status='active'
                              AND u.user_type='staff'
                            ORDER BY wa.staff_id ASC";

                    $rows = $this->db->query($sql)->getResult();

                    $result = [];
                    foreach ($rows as $r) {
                        $result[] = [
                            "staff_id"   => (int)$r->staff_id,
                            "staff_name" => trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? '')),
                            "start_time" => (string)$r->start_time, // time
                            "end_time"   => (string)$r->end_time,   // time
                        ];
                    }

                    return $result;
                }


}
