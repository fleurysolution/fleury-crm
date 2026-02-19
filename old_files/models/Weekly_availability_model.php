<?php

namespace App\Models;

class Weekly_availability_model extends Crud_model
{
    protected $table = null;

    function __construct()
    {
        $this->table = "weekly_availability";
        parent::__construct($this->table);
    }

    public function is_valid_time_range(string $start, string $end): bool
    {
        $s = strtotime("1970-01-01 " . $start);
        $e = strtotime("1970-01-01 " . $end);
        return ($s !== false && $e !== false && $e > $s);
    }

    public function get_staff_weekly_availability(int $staff_id): array
    {
        $t = $this->db->prefixTable("weekly_availability");
        $rows = $this->db->query("SELECT * FROM $t WHERE staff_id=? AND deleted=0", [$staff_id])->getResult();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->day_of_week] = $r;
        }
        return $map;
    }

    public function get_staff_day_window(int $staff_id, string $day)
    {
        $t = $this->db->prefixTable("weekly_availability");
        return $this->db->query("SELECT * FROM $t WHERE staff_id=? AND day_of_week=? AND deleted=0 LIMIT 1", [$staff_id, $day])->getRow();
    }

    public function upsert_staff_day_window(int $staff_id, string $day, string $start, string $end, int $is_available): bool
    {
        $existing = $this->get_staff_day_window($staff_id, $day);

        $data = [
            "staff_id"      => $staff_id,
            "day_of_week"   => $day,
            "start_time"    => $start,
            "end_time"      => $end,
            "is_available"  => $is_available ? 1 : 0,
            "deleted"       => 0,
            "updated_at"    => date("Y-m-d H:i:s"),
        ];

        if ($existing && !empty($existing->id)) {
            $this->ci_save($data, (int)$existing->id);
            return true;
        }

        $data["created_at"] = date("Y-m-d H:i:s");
        $id = $this->ci_save($data);
        return (bool)$id;
    }
}
