<?php

namespace App\Models;

class Appointment_breaks_model extends Crud_model
{
    protected $table = 'weekly_breaks';

    function __construct()
    {
        $this->table = 'weekly_breaks';
        parent::__construct($this->table);
    }

    /**
     * Returns breaks for staff on a specific weekday (Mon..Sun).
     */
    public function get_breaks_for_day(int $staff_id, string $day_of_week)
    {
        $table = $this->db->prefixTable('weekly_breaks');

        $staff_id = (int)$staff_id;
        $day_of_week = $this->_get_clean_value($day_of_week);

        $sql = "SELECT id, staff_id, day_of_week, start_time, end_time, title
                FROM $table
                WHERE deleted=0 AND is_active=1
                  AND staff_id=$staff_id
                  AND day_of_week='$day_of_week'
                ORDER BY start_time ASC";

        return $this->db->query($sql);
    }
}
