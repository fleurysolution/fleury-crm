<?php

namespace App\Models;

class Weekly_breaks_model extends Crud_model {

    protected $table = 'weekly_breaks';

    function __construct() {
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $table = $this->db->prefixTable('weekly_breaks');
        $where = " AND $table.deleted=0";

        $staff_id = $this->_get_clean_value($options, "staff_id");
        if ($staff_id) {
            $where .= " AND $table.staff_id=$staff_id";
        }

        $day_of_week = $this->_get_clean_value($options, "day_of_week");
        if ($day_of_week) {
            $where .= " AND $table.day_of_week=" . $this->db->escape($day_of_week);
        }

        $sql = "SELECT $table.*
                FROM $table
                WHERE 1=1 $where
                ORDER BY $table.break_start ASC";

        return $this->db->query($sql);
    }

     public function is_valid_time_range(string $start, string $end): bool
    {
        $s = strtotime("1970-01-01 " . $start);
        $e = strtotime("1970-01-01 " . $end);
        return ($s !== false && $e !== false && $e > $s);
    }

    public function is_within_window(string $bStart, string $bEnd, string $wStart, string $wEnd): bool
    {
        $bs = strtotime("1970-01-01 " . $bStart);
        $be = strtotime("1970-01-01 " . $bEnd);
        $ws = strtotime("1970-01-01 " . $wStart);
        $we = strtotime("1970-01-01 " . $wEnd);
        return ($bs >= $ws && $be <= $we);
    }

    public function get_staff_weekly_breaks(int $staff_id): array
    {
        $t = $this->db->prefixTable("weekly_breaks");
        $rows = $this->db->query("SELECT * FROM $t WHERE staff_id=? AND deleted=0 AND is_active=1 ORDER BY day_of_week, start_time", [$staff_id])->getResult();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->day_of_week][] = $r;
        }
        return $map;
    }

    public function break_overlaps_existing(int $staff_id, string $day, string $start, string $end): bool
    {
        $t = $this->db->prefixTable("weekly_breaks");
        $sql = "SELECT id FROM $t
                WHERE staff_id=? AND day_of_week=? AND deleted=0 AND is_active=1
                  AND NOT (end_time<=? OR start_time>=?)
                LIMIT 1";
        $row = $this->db->query($sql, [$staff_id, $day, $start, $end])->getRow();
        return (bool)$row;
    }

    public function add_break(int $staff_id, string $day, string $start, string $end, string $title): ?int
    {
        $data = [
            "staff_id"    => $staff_id,
            "day_of_week" => $day,
            "start_time"  => $start,
            "end_time"    => $end,
            "title"       => $title,
            "is_active"   => 1,
            "deleted"     => 0,
        ];

        $id = $this->ci_save($data);
        return $id ? (int)$id : null;
    }

    public function soft_delete_break(int $id): bool
    {
        return (bool)$this->ci_save(["deleted" => 1, "is_active" => 0], $id);
    }
}
