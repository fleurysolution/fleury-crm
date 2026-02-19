<?php

namespace App\Models;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DateInterval;

class Appointment_services_model extends Crud_model
{
    protected $table = 'customer_appointments';
    protected $allowedFields = ['name', 'email', 'date', 'time', 'phone', 'duration', 'message'];
    protected $useTimestamps = true;
     
    function __construct() {
        $this->table = 'customer_appointments';
        parent::__construct($this->table);
    }
      public function isSlotAvailable($date, $time)
    {
       $builder= $this->db->prefixTable('customer_appointments');
       $sql='select * from 0 where date="'.$date.'" and time="'.$time.'"'; //die;
         return $this->db->query($sql);
      
    }

     function get_details($options = array()) {
        $customer_appointments = $this->db->prefixTable('customer_appointments');
        $service_categories = $this->db->prefixTable('service_categories');
        $services_table = $this->db->prefixTable('services');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");       
        $service_id = $this->_get_clean_value($options, "service_id");
        if ($id) {
            $where .= " AND $customer_appointments.id=$id";
        }
        if ($service_id) {
            $where .= " AND $customer_appointments.service_id=$id";
        }
        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($customer_appointments.name LIKE '%$search%' ESCAPE '!' OR $customer_appointments.email LIKE '%$search%' ESCAPE '!' OR $customer_appointments.phone LIKE '%$search%' ESCAPE '!')";
        }        

        $limit_query = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $offset = $this->_get_clean_value($options, "offset");
            $limit_query = "LIMIT $offset, $limit";
        }

        $sql = "SELECT $customer_appointments.*, $services_table.name as services_title
        FROM $customer_appointments   
        LEFT JOIN $services_table ON $services_table.id= $customer_appointments.service_id    
        WHERE $customer_appointments.deleted=0 $where
        ORDER BY $customer_appointments.created_at DESC 
        $limit_query";
        
        return $this->db->query($sql);
    }


        public function getBookingsForStaffDate(int $staff_id, string $date)
        {
            $table = $this->db->prefixTable('customer_appointments');

            // Day window
            $dayStart = $date . ' 00:00:00';
            $dayEnd   = $date . ' 23:59:59';

            $sql = "SELECT id, start_time, end_time, status, deleted
                    FROM $table
                    WHERE deleted = 0
                      AND staff_id = ?
                      AND status NOT IN ('cancelled')
                      AND start_time <= ?
                      AND end_time >= ?
                    ORDER BY start_time ASC";

            // start_time <= dayEnd AND end_time >= dayStart ensures overlap with the day
            return $this->db->query($sql, [$staff_id, $dayEnd, $dayStart]);
        }


        public function is_staff_available($staff_id, $start_dt, $end_dt) {
                $weekly = $this->db->prefixTable('weekly_availability');
                $appointments = $this->db->prefixTable('customer_appointments');

                $staff_id = (int)$staff_id;

                // Determine day short: Mon/Tue...
                $day_short = date('D', strtotime($start_dt)); // Mon, Tue, Wed...

                // 1) Weekly availability check
                $sqlAvail = "SELECT *
                            FROM $weekly
                            WHERE staff_id=$staff_id
                              AND day_of_week='$day_short'
                              AND deleted=0
                              AND is_available=1
                            LIMIT 1";
                $avail = $this->db->query($sqlAvail)->getRow();
                if (!$avail) {
                    return false;
                }

                $start_time = date('H:i:s', strtotime($start_dt));
                $end_time   = date('H:i:s', strtotime($end_dt));

                // Must be within working hours
                if (!($start_time >= $avail->start_time && $end_time <= $avail->end_time)) {
                    return false;
                }

                // Must not overlap break if break defined
                if (!empty($avail->break_start_time) && !empty($avail->break_end_time)) {
                    $bs = $avail->break_start_time;
                    $be = $avail->break_end_time;

                    // overlap test: [start,end) overlaps [bs,be)
                    if ($start_time < $be && $end_time > $bs) {
                        return false;
                    }
                }

                // 2) Appointment conflict check (exclude cancelled, deleted)
                // overlap test: existing.start < requested.end AND existing.end > requested.start
                $sqlConflict = "SELECT id
                                FROM $appointments
                                WHERE deleted=0
                                  AND staff_id=$staff_id
                                  AND status NOT IN ('cancelled')
                                  AND start_time < " . $this->db->escape($end_dt) . "
                                  AND end_time > " . $this->db->escape($start_dt) . "
                                LIMIT 1";
                $conflict = $this->db->query($sqlConflict)->getRow();
                if ($conflict) {
                    return false;
                }

                return true;
            }

     public function pick_best_available_staff(string $start_dt, string $end_dt): ?int
        {
            try {
                $users  = $this->db->prefixTable('users');                 // pcm_users
                $weekly = $this->db->prefixTable('weekly_availability');   // pcm_weekly_availability
                $appts  = $this->db->prefixTable('customer_appointments'); // pcm_customer_appointments

                $start_ts = strtotime($start_dt);
                $end_ts   = strtotime($end_dt);

                if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
                    return null;
                }

                // Mon/Tue/Wed...
                $dayShort = date('D', $start_ts);
                $dayShort = substr($dayShort, 0, 3);

                // 1) Candidate staff: active staff users
                $sqlStaff = "SELECT id
                            FROM $users
                            WHERE deleted=0
                            AND status='active'
                            AND user_type='staff'";

                $staffRows = $this->db->query($sqlStaff)->getResult();
                if (!$staffRows) {
                    return null;
                }

                $candidates = [];

                foreach ($staffRows as $s) {
                    $staff_id = (int)$s->id;

                    // 2) Weekly availability entry for this day
                    $sqlAvail = "SELECT start_time, end_time, is_available
                                FROM $weekly
                                WHERE deleted=0
                                AND staff_id=$staff_id
                                AND day_of_week=" . $this->db->escape($dayShort) . "
                                LIMIT 1";

                    $avail = $this->db->query($sqlAvail)->getRow();
                    if (!$avail || (int)$avail->is_available !== 1) {
                        continue;
                    }

                    // Build "today" window for this staff
                    $dateYmd   = date('Y-m-d', $start_ts);
                    $availFrom = strtotime($dateYmd . ' ' . $avail->start_time);
                    $availTo   = strtotime($dateYmd . ' ' . $avail->end_time);

                    // Our requested slot must be fully inside availability
                    if ($start_ts < $availFrom || $end_ts > $availTo) {
                        continue;
                    }

                    // 3) Conflict check against other appointments for that staff
                    $sqlConf = "SELECT id
                                FROM $appts
                                WHERE deleted=0
                                AND staff_id=$staff_id
                                AND status IN ('pending','confirmed')
                                AND (" . $this->db->escape($start_dt) . " < end_time
                                AND " . $this->db->escape($end_dt) . " > start_time)
                                LIMIT 1";

                    $conf = $this->db->query($sqlConf)->getRow();
                    if ($conf) {
                        continue;
                    }

                    $candidates[] = $staff_id;
                }

                if (!$candidates) {
                    return null;
                }

                // 4) Round-robin: pick staff with oldest last assignment (or never assigned)
                $ids = implode(',', array_map('intval', $candidates));

                $sqlRR = "SELECT staff_id, MAX(created_at) AS last_assigned
                        FROM $appts
                        WHERE deleted=0
                            AND staff_id IN ($ids)
                        GROUP BY staff_id
                        ORDER BY last_assigned ASC";

                $rrRows = $this->db->query($sqlRR)->getResult();

                $lastMap = [];
                foreach ($rrRows as $r) {
                    $lastMap[(int)$r->staff_id] = $r->last_assigned ?: null;
                }

                $best   = null;
                $bestTs = PHP_INT_MAX;

                foreach ($candidates as $sid) {
                    // Prefer staff with no appointment history at all
                    if (!isset($lastMap[$sid])) {
                        return (int)$sid;
                    }

                    $ts = strtotime($lastMap[$sid] ?? '') ?: 0;
                    if ($ts < $bestTs) {
                        $bestTs = $ts;
                        $best   = $sid;
                    }
                }

                return $best ? (int)$best : (int)$candidates[0];

            } catch (\Throwable $e) {
                // If something goes wrong here, we DO NOT want booking_save() to explode with server_error.
                log_message('error', 'pick_best_available_staff failed: ' . $e->getMessage());
                return null;
            }
        }


                private function _slot_hits_break(
                    int $staff_id,
                    string $dayShort,
                    string $dateLocal,
                    string $startLocalTime,
                    string $endLocalTime,
                    string $companyTz
                ): bool {
                    $Breaks = model('App\Models\Appointment_breaks_model');
                    $breaks = $Breaks->get_breaks_for_day($staff_id, $dayShort)->getResult();

                    if (!$breaks) return false;

                    $slotStart = new \DateTime($dateLocal.' '.$startLocalTime, new \DateTimeZone($companyTz));
                    $slotEnd   = new \DateTime($dateLocal.' '.$endLocalTime, new \DateTimeZone($companyTz));

                    foreach ($breaks as $b) {
                        $bStart = new \DateTime($dateLocal.' '.$b->start_time, new \DateTimeZone($companyTz));
                        $bEnd   = new \DateTime($dateLocal.' '.$b->end_time, new \DateTimeZone($companyTz));

                        if ($slotStart < $bEnd && $slotEnd > $bStart) {
                            return true;
                        }
                    }

                    return false;
                }



          public function is_staff_available_for_slot(int $staff_id, string $start_dt, string $end_dt): bool
            {
                $weekly = $this->db->prefixTable('weekly_availability');
                $appointments = $this->db->prefixTable('customer_appointments');

                $day_short = date('D', strtotime($start_dt)); // Mon/Tue...
                $start_time = date('H:i:s', strtotime($start_dt));
                $end_time   = date('H:i:s', strtotime($end_dt));

                // 1) Must be within weekly availability AND not overlap break
                $sql1 = "
                    SELECT 1
                    FROM $weekly w
                    WHERE w.deleted=0
                      AND w.is_available=1
                      AND w.staff_id=?
                      AND w.day_of_week=?
                      AND ? >= w.start_time
                      AND ? <= w.end_time
                      AND (
                          w.break_start_time IS NULL OR w.break_end_time IS NULL
                          OR NOT ( ? < w.break_end_time AND ? > w.break_start_time )
                      )
                    LIMIT 1
                ";
                $ok = $this->db->query($sql1, [$staff_id, $day_short, $start_time, $end_time, $start_time, $end_time])->getRow();
                if (!$ok) {
                    return false;
                }

                // 2) Must NOT conflict with an existing appointment
                $sql2 = "
                    SELECT 1
                    FROM $appointments a
                    WHERE a.deleted=0
                      AND a.staff_id=?
                      AND a.status NOT IN ('cancelled')
                      AND a.start_time < ?
                      AND a.end_time > ?
                    LIMIT 1
                ";
                $conflict = $this->db->query($sql2, [$staff_id, $end_dt, $start_dt])->getRow();

                return $conflict ? false : true;
            }

            // App/Models/Users_model.php
            public function get_active_staff_basic_list()
            {
                $users = $this->db->prefixTable('users');

                $sql = "SELECT id, first_name, last_name, email
                        FROM $users
                        WHERE deleted=0
                          AND user_type='staff'
                          AND status='active'
                          AND disable_login=0
                        ORDER BY first_name ASC, last_name ASC";

                return $this->db->query($sql)->getResult();
            }

            public function assign_staff(int $appointment_id, int $staff_id, int $assigned_by): bool
                {
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

                    return (bool) $this->db->query($sql);
                }

                public function get_one_active(int $appointment_id)
                {
                    $appointments = $this->db->prefixTable('customer_appointments');
                    $appointment_id = (int)$appointment_id;

                    $sql = "SELECT *
                            FROM $appointments
                            WHERE id=$appointment_id AND deleted=0
                            LIMIT 1";

                    return $this->db->query($sql)->getRow();
                }


                private function _overlaps(string $startA, string $endA, string $startB, string $endB): bool
                {
                    // overlap if startA < endB AND endA > startB
                    return (strtotime($startA) < strtotime($endB)) && (strtotime($endA) > strtotime($startB));
                }

                private function _weekdayShortFromDate(string $date): string
                {
                    // date: YYYY-MM-DD
                    $ts = strtotime($date);
                    return date('D', $ts); // Mon, Tue...
                }

                private function _dt(string $dateTime, string $tz): \DateTime
                {
                    return new \DateTime($dateTime, new \DateTimeZone($tz));
                }

                

                    public function get_breaks_for_staff_day(int $staff_id, string $dayShort): array
                    {
                        $Breaks = model('App\Models\Appointment_breaks_model');
                        $rows = $Breaks->get_breaks_for_day($staff_id, $dayShort)->getResult();

                        $result = [];
                        foreach ($rows as $r) {
                            $result[] = [
                                "title"      => (string)($r->title ?? 'Break'),
                                "start_time" => (string)$r->start_time, // time
                                "end_time"   => (string)$r->end_time,   // time
                            ];
                        }
                        return $result;
                    }


                    public function build_available_slots_payload(
                        int $service_id,
                        string $date,
                        int $durationMinutes,
                        string $userTz,
                        string $companyTz
                    ): array
                    {
                        $Availability = model('App\Models\Appointment_availability_model');

                        $dayShort = $this->_weekdayShortFromDate($date); // Mon..Sun

                        // Get all staff windows for that weekday
                        $windows = $Availability->get_active_staff_weekly_windows_for_day($dayShort);

                        if (!$windows) {
                            return [
                                "success" => true,
                                "timezone_label" => $userTz,
                                "availability" => [],
                                "breaks" => [],
                                "slots" => []
                            ];
                        }

                        $slotsMap = []; // key=startUtc => slot data + available staff list
                        $availabilitySummary = [];
                        $breaksSummary = [];

                        foreach ($windows as $w) {
                            $staff_id = (int)$w["staff_id"];

                            // Build availability datetime in company TZ
                            $availStartCompany = $this->_dt($date . ' ' . $w["start_time"], $companyTz);
                            $availEndCompany   = $this->_dt($date . ' ' . $w["end_time"], $companyTz);

                            if ($availEndCompany <= $availStartCompany) {
                                continue;
                            }

                            // Store summary (converted to user TZ)
                            $tmpStart = clone $availStartCompany; $tmpStart->setTimezone(new \DateTimeZone($userTz));
                            $tmpEnd   = clone $availEndCompany;   $tmpEnd->setTimezone(new \DateTimeZone($userTz));
                            $availabilitySummary[] = [
                                "label" => $w["staff_name"] ?: ("Staff #" . $staff_id),
                                "start" => $tmpStart->format('h:i A'),
                                "end"   => $tmpEnd->format('h:i A')
                            ];

                            // Breaks for staff/day (company TZ time values)
                            $breaks = $this->get_breaks_for_staff_day($staff_id, $dayShort);

                            foreach ($breaks as $b) {
                                // summary (user tz)
                                $bStart = $this->_dt($date . ' ' . $b["start_time"], $companyTz);
                                $bEnd   = $this->_dt($date . ' ' . $b["end_time"], $companyTz);
                                $bStartU = clone $bStart; $bStartU->setTimezone(new \DateTimeZone($userTz));
                                $bEndU   = clone $bEnd;   $bEndU->setTimezone(new \DateTimeZone($userTz));
                                $breaksSummary[] = [
                                    "title" => $b["title"] ?: "Break",
                                    "start" => $bStartU->format('h:i A'),
                                    "end"   => $bEndU->format('h:i A')
                                ];
                            }

                            // Generate candidate slots in company TZ
                            $cursor = clone $availStartCompany;

                            while (true) {
                                $slotStartCompany = clone $cursor;
                                $slotEndCompany   = (clone $cursor)->modify("+{$durationMinutes} minutes");

                                if ($slotEndCompany > $availEndCompany) {
                                    break;
                                }

                                // Exclude break overlap
                                $blockedByBreak = false;
                                foreach ($breaks as $b) {
                                    $bStart = $this->_dt($date . ' ' . $b["start_time"], $companyTz);
                                    $bEnd   = $this->_dt($date . ' ' . $b["end_time"], $companyTz);
                                    if ($slotStartCompany < $bEnd && $slotEndCompany > $bStart) {
                                        $blockedByBreak = true;
                                        break;
                                    }
                                }

                                if (!$blockedByBreak) {
                                    // Convert to UTC for storage/comparison
                                    $slotStartUtc = clone $slotStartCompany; $slotStartUtc->setTimezone(new \DateTimeZone('UTC'));
                                    $slotEndUtc   = clone $slotEndCompany;   $slotEndUtc->setTimezone(new \DateTimeZone('UTC'));

                                    $startUtcStr = $slotStartUtc->format('Y-m-d H:i:s');
                                    $endUtcStr   = $slotEndUtc->format('Y-m-d H:i:s');

                                    // Exclude conflicts
                                    if (!$this->staff_has_conflict($staff_id, $startUtcStr, $endUtcStr)) {
                                        // Display in user timezone
                                        $dispStart = clone $slotStartCompany; $dispStart->setTimezone(new \DateTimeZone($userTz));

                                        $key = $startUtcStr;
                                        if (!isset($slotsMap[$key])) {
                                            $slotsMap[$key] = [
                                                "start_utc" => $startUtcStr,
                                                "end_utc"   => $endUtcStr,
                                                "display"   => $dispStart->format('h:i A'),
                                                "_staff"    => []
                                            ];
                                        }
                                        $slotsMap[$key]["_staff"][] = $staff_id;
                                    }
                                }

                                // Step = durationMinutes (standard)
                                $cursor->modify("+{$durationMinutes} minutes");
                            }
                        }

                        // Convert to array & remove internal staff list
                        ksort($slotsMap);
                        $slots = [];
                        foreach ($slotsMap as $s) {
                            $slots[] = [
                                "start_utc" => $s["start_utc"],
                                "end_utc"   => $s["end_utc"],
                                "display"   => $s["display"],
                                "capacity"  => count($s["_staff"]) // optional, useful for UI
                            ];
                        }

                        // Optional: de-duplicate breaks in summary
                        $breaksSummary = $this->_dedupe_breaks_summary($breaksSummary);

                        return [
                            "success" => true,
                            "timezone_label" => $userTz,
                            "availability" => $availabilitySummary,
                            "breaks" => $breaksSummary,
                            "slots" => $slots
                        ];
                    }

                    private function _dedupe_breaks_summary(array $breaks): array
                    {
                        $seen = [];
                        $out = [];
                        foreach ($breaks as $b) {
                            $k = ($b["title"] ?? '') . '|' . ($b["start"] ?? '') . '|' . ($b["end"] ?? '');
                            if (isset($seen[$k])) continue;
                            $seen[$k] = true;
                            $out[] = $b;
                        }
                        return $out;
                    }



                    public function build_available_dates_payload(
    int $service_id,
    string $fromDate,
    string $toDate,
    int $durationMinutes,
    string $userTz,
    string $companyTz
): array {
    // Safety bounds
    $fromTs = strtotime($fromDate);
    $toTs   = strtotime($toDate);
    if (!$fromTs || !$toTs || $toTs < $fromTs) {
        return [
            "success" => false,
            "message" => "Invalid date range."
        ];
    }

    // Hard cap to prevent heavy load (max 62 days)
    $maxDays = 62;
    $days = (int)floor(($toTs - $fromTs) / 86400) + 1;
    if ($days > $maxDays) {
        $toTs = strtotime("+".($maxDays-1)." days", $fromTs);
        $toDate = date('Y-m-d', $toTs);
    }

    $availableDates = [];

    // We consider a date "available" if build_available_slots_payload returns at least 1 slot
    for ($ts = $fromTs; $ts <= $toTs; $ts += 86400) {
        $date = date('Y-m-d', $ts);

        $payload = $this->build_available_slots_payload(
            $service_id,
            $date,
            $durationMinutes,
            $userTz,
            $companyTz
        );

        if (!empty($payload["slots"])) {
            $availableDates[] = $date;
        }
    }

    return [
        "success" => true,
        "timezone_label" => $userTz,
        "from" => $fromDate,
        "to" => $toDate,
        "available_dates" => $availableDates
    ];
}


public function prepare_checkout_payload(int $appointment_id): ?array
{
    // Re-use existing get_details() to fetch appointment with service title
    $options     = ["id" => $appointment_id];
    $appointment = $this->get_details($options)->getRow();

    if (!$appointment) {
        return null;
    }

    // Normalize amount
    $amount = (float)($appointment->price ?? 0);

    // If already paid, no payment required
    $status           = $appointment->payment_status ?? 'unpaid';
    $already_paid     = ($status === 'paid');
    $requires_payment = !$already_paid && $amount > 0;

    return [
        'appointment'      => $appointment,
        'amount'           => $amount,
        'requires_payment' => $requires_payment,
    ];
}

public function normalize_and_validate_slot_utc(string $start_raw, string $end_raw, int $duration_minutes): array
{
    $start_ts = strtotime($start_raw);
    $end_ts   = strtotime($end_raw);

    if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
        return ["success" => false, "code" => "invalid_time"];
    }
    if ($start_ts < time()) {
        return ["success" => false, "code" => "past_time"];
    }
    if ($duration_minutes <= 0) {
        return ["success" => false, "code" => "invalid_service"];
    }

    $start_dt = gmdate('Y-m-d H:i:s', $start_ts);
    $end_dt   = gmdate('Y-m-d H:i:s', strtotime("+{$duration_minutes} minutes", $start_ts));

    return [
        "success"  => true,
        "start_dt" => $start_dt,
        "end_dt"   => $end_dt,
    ];
}

/**
 * Assign staff using:
 * - service round_robin_enabled
 * - availability + breaks
 * - existing appointment conflicts
 * - fair round-robin ordering (persisted)
 */
public function assign_staff_for_service(int $service_id, string $start_dt_utc, string $end_dt_utc): ?int
{
    // 1) Get eligible staff pool (active staff)
    $staff_ids = $this->get_active_staff_ids();
    if (!$staff_ids) return null;

    // 2) Filter to staff available for that slot
    $eligible = $this->filter_staff_by_availability_breaks_conflicts($staff_ids, $start_dt_utc, $end_dt_utc);
    if (!$eligible) return null;

    // 3) If service has round-robin enabled, rotate fairly; else pick “best available”
    $service = model('Services_model')->get_details(["id" => $service_id])->getRow();
    $rr = (int)($service->round_robin_enabled ?? 1);

    if ($rr) {
        return $this->pick_round_robin_staff($service_id, $eligible);
    }

    return $eligible[0] ?? null;
}

public function create_customer_appointment(array $data): int
{
    // Keep all insert logic here
    $id = $this->ci_save($data);
    return $id ? (int)$id : 0;
}




    /* =========================================================
     * WEEKLY AVAILABILITY + BREAKS (Mon..Sun enum)
     * ========================================================= */
    private function dayEnumFromDate(\DateTimeInterface $dtInOrgTz): string
    {
        // 'D' => Mon/Tue/Wed/Thu/Fri/Sat/Sun
        return $dtInOrgTz->format('D');
    }

    public function get_staff_weekly_windows(int $staff_id, \DateTimeInterface $dateInOrgTz): array
    {
        $wa  = $this->db->prefixTable('weekly_availability');
        $dow = $this->dayEnumFromDate($dateInOrgTz);

        $sql = "SELECT start_time, end_time, break_start_time, break_end_time
                FROM {$wa}
                WHERE deleted = 0
                  AND is_available = 1
                  AND staff_id = ?
                  AND day_of_week = ?";

        $rows = $this->db->query($sql, [$staff_id, $dow])->getResult() ?: [];

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'start' => (string)$r->start_time,
                'end'   => (string)$r->end_time,
                // legacy fallback break (optional)
                'break_start_time' => $r->break_start_time ? (string)$r->break_start_time : null,
                'break_end_time'   => $r->break_end_time ? (string)$r->break_end_time : null,
            ];
        }
        return $out;
    }

    public function get_staff_weekly_breaks(int $staff_id, \DateTimeInterface $dateInOrgTz): array
    {
        $wb  = $this->db->prefixTable('weekly_breaks');
        $dow = $this->dayEnumFromDate($dateInOrgTz);

        $sql = "SELECT start_time, end_time, title
                FROM {$wb}
                WHERE deleted = 0
                  AND is_active = 1
                  AND staff_id = ?
                  AND day_of_week = ?";

        $rows = $this->db->query($sql, [$staff_id, $dow])->getResult() ?: [];

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'start' => (string)$r->start_time,
                'end'   => (string)$r->end_time,
                'title' => (string)($r->title ?? 'Break'),
            ];
        }

        // fallback: if no rows in weekly_breaks, use single-break columns from weekly_availability
        if (!$out) {
            $wins = $this->get_staff_weekly_windows($staff_id, $dateInOrgTz);
            foreach ($wins as $w) {
                if (!empty($w['break_start_time']) && !empty($w['break_end_time'])) {
                    $out[] = [
                        'start' => $w['break_start_time'],
                        'end'   => $w['break_end_time'],
                        'title' => 'Break',
                    ];
                }
            }
        }

        return $out;
    }




    private function overlaps(\DateTimeInterface $aStart, \DateTimeInterface $aEnd, \DateTimeInterface $bStart, \DateTimeInterface $bEnd): bool
    {
        return ($aStart < $bEnd) && ($aEnd > $bStart);
    }

    /* =========================================================
     * ROUND ROBIN STATE (per service)
     * ========================================================= */
    private function rr_ensure_row(int $service_id): void
    {
        $st = $this->db->prefixTable('service_round_robin_state');
        $this->db->query("INSERT IGNORE INTO {$st} (service_id, last_staff_id) VALUES (?, NULL)", [$service_id]);
    }

    private function rr_get_for_update(int $service_id)
    {
        $st = $this->db->prefixTable('service_round_robin_state');
        return $this->db->query("SELECT * FROM {$st} WHERE service_id=? FOR UPDATE", [$service_id])->getRow();
    }

    private function rr_set_last(int $service_id, int $staff_id): void
    {
        $st = $this->db->prefixTable('service_round_robin_state');
        $this->db->query("UPDATE {$st} SET last_staff_id=?, updated_at=CURRENT_TIMESTAMP WHERE service_id=?", [$staff_id, $service_id]);
    }

    private function rr_pick_next(array $eligible, ?int $last): ?int
    {
        $eligible = array_values(array_unique(array_map('intval', $eligible)));
        sort($eligible);
        if (!$eligible) return null;

        if (!$last) return $eligible[0];

        foreach ($eligible as $sid) {
            if ($sid > $last) return $sid;
        }
        return $eligible[0]; // wrap
    }

    /* =========================================================
     * ELIGIBILITY FOR A SLOT
     * ========================================================= */
    public function get_eligible_staff_ids_for_slot(int $service_id, string $slotStartUtc, string $slotEndUtc, string $orgTimezone): array
    {
        $staffIds = $this->get_active_staff_ids();
        if (!$staffIds) return [];

        $slotStart = new \DateTimeImmutable($slotStartUtc, new \DateTimeZone('UTC'));
        $slotEnd   = new \DateTimeImmutable($slotEndUtc, new \DateTimeZone('UTC'));

        $orgTz = new \DateTimeZone($orgTimezone);
        $dateInOrgTz = $slotStart->setTimezone($orgTz);

        $eligible = [];

        foreach ($staffIds as $sid) {
            $windows = $this->get_staff_weekly_windows($sid, $dateInOrgTz);
            if (!$windows) continue;

            // Must be contained in at least one availability window
            $contained = false;
            foreach ($windows as $w) {
                $wStart = new \DateTimeImmutable($dateInOrgTz->format('Y-m-d') . ' ' . $w['start'], $orgTz);
                $wEnd   = new \DateTimeImmutable($dateInOrgTz->format('Y-m-d') . ' ' . $w['end'], $orgTz);

                $wStartUtc = $wStart->setTimezone(new \DateTimeZone('UTC'));
                $wEndUtc   = $wEnd->setTimezone(new \DateTimeZone('UTC'));

                if ($slotStart >= $wStartUtc && $slotEnd <= $wEndUtc) {
                    $contained = true;
                    break;
                }
            }
            if (!$contained) continue;

            // Break exclusion
            $breaks = $this->get_staff_weekly_breaks($sid, $dateInOrgTz);
            $breakHit = false;
            foreach ($breaks as $b) {
                $bStart = new \DateTimeImmutable($dateInOrgTz->format('Y-m-d') . ' ' . $b['start'], $orgTz);
                $bEnd   = new \DateTimeImmutable($dateInOrgTz->format('Y-m-d') . ' ' . $b['end'], $orgTz);

                $bStartUtc = $bStart->setTimezone(new \DateTimeZone('UTC'));
                $bEndUtc   = $bEnd->setTimezone(new \DateTimeZone('UTC'));

                if ($this->overlaps($slotStart, $slotEnd, $bStartUtc, $bEndUtc)) {
                    $breakHit = true;
                    break;
                }
            }
            if ($breakHit) continue;

            // Conflicts
            if ($this->staff_has_conflict($sid, $slotStartUtc, $slotEndUtc)) continue;

            $eligible[] = $sid;
        }

        return $eligible;
    }

    /* =========================================================
     * STAFF ASSIGNMENT (ROUND ROBIN)
     * ========================================================= */
    public function assign_staff_round_robin(int $service_id, string $slotStartUtc, string $slotEndUtc, string $orgTimezone): ?int
    {
        $eligible = $this->get_eligible_staff_ids_for_slot($service_id, $slotStartUtc, $slotEndUtc, $orgTimezone);
        if (!$eligible) return null;

        // RR persistence
        $this->db->transBegin();
        try {
            $this->rr_ensure_row($service_id);
            $state = $this->rr_get_for_update($service_id);
            $last  = $state ? (int)($state->last_staff_id ?? 0) : 0;

            $picked = $this->rr_pick_next($eligible, $last);
            if (!$picked) {
                $this->db->transRollback();
                return null;
            }

            $this->rr_set_last($service_id, $picked);
            $this->db->transCommit();
            return (int)$picked;

        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'assign_staff_round_robin failed: ' . $e->getMessage());
            return null;
        }
    }

    public function get_service_scheduling_rules(int $service_id): array
{
    $svc = $this->db->prefixTable('services');

    $row = $this->db->query(
        "SELECT id, duration_minutes, slot_interval_minutes, buffer_before_minutes, buffer_after_minutes,
                min_notice_minutes, max_advance_days, assignment_mode
         FROM {$svc}
         WHERE deleted = 0 AND is_active = 1 AND id = ?",
        [$service_id]
    )->getRow();

    if (!$row) {
        return [];
    }

    return [
        "duration" => (int)$row->duration_minutes,
        "slot_interval" => max(5, (int)$row->slot_interval_minutes),
        "buffer_before" => max(0, (int)$row->buffer_before_minutes),
        "buffer_after" => max(0, (int)$row->buffer_after_minutes),
        "min_notice" => max(0, (int)$row->min_notice_minutes),
        "max_advance_days" => max(1, (int)$row->max_advance_days),
        "assignment_mode" => $row->assignment_mode ?: "manual",
    ];
}


// New final code

    /* =========================================================
     * Core config helpers
     * ========================================================= */

    public function get_app_timezone(): DateTimeZone {
        // Uses your setting: get_setting("timezone")
        $tz = get_setting("timezone");
        if (!$tz) { $tz = "UTC"; }
        try { return new DateTimeZone($tz); } catch (\Throwable $e) { return new DateTimeZone("UTC"); }
    }

    public function toUtcIso(DateTime $dt): string {
        $utc = clone $dt;
        $utc->setTimezone(new DateTimeZone("UTC"));
        return $utc->format("Y-m-d\TH:i:s\Z");
    }

    public function dtFromDateAndTimeInAppTz(string $ymd, string $timeHHMMSS): DateTime {
        $tz = $this->get_app_timezone();
        return new DateTime($ymd . " " . $timeHHMMSS, $tz);
    }

    public function dtFromUtcIso(string $iso): DateTime {
        // Accepts "2025-01-01T10:00:00Z" or any strtotime-supported
        $ts = strtotime($iso);
        $dt = new DateTime("@".$ts);
        $dt->setTimezone(new DateTimeZone("UTC"));
        return $dt;
    }

    public function formatInTimezone(DateTime $utc, string $tzName, string $format = "h:i A"): string {
        $tz = new DateTimeZone($tzName ?: "UTC");
        $local = clone $utc;
        $local->setTimezone($tz);
        return $local->format($format);
    }

    public function get_service(int $service_id) {
        $services = $this->db->prefixTable("services");
        return $this->db->query("SELECT * FROM $services WHERE deleted=0 AND is_active=1 AND id=?", [$service_id])->getRow();
    }

    public function get_active_staff_ids(): array {
        $users = $this->db->prefixTable("users");
        $rows = $this->db->query("SELECT id FROM $users WHERE deleted=0 AND status='active' AND user_type='staff'")->getResult();
        return array_map(fn($r) => (int)$r->id, $rows ?: []);
    }

    /* =========================================================
     * Availability + breaks (your tables)
     * pcm_weekly_availability: day_of_week enum('Mon'..'Sun')
     * pcm_weekly_breaks: day_of_week enum('Mon'..'Sun')
     * ========================================================= */

    public function dayEnumFromYmd(string $ymd): string {
        // returns Mon/Tue/...
        $ts = strtotime($ymd);
        return date("D", $ts); // Mon Tue Wed Thu Fri Sat Sun
    }

    public function get_staff_availability_windows(int $staff_id, string $ymd): array {
        $day = $this->dayEnumFromYmd($ymd);
        $wa = $this->db->prefixTable("weekly_availability");

        $rows = $this->db->query("
            SELECT start_time, end_time
            FROM $wa
            WHERE deleted=0 AND is_available=1 AND staff_id=? AND day_of_week=?
        ", [$staff_id, $day])->getResult();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                "start" => $this->dtFromDateAndTimeInAppTz($ymd, $r->start_time)->setTimezone(new DateTimeZone("UTC")),
                "end"   => $this->dtFromDateAndTimeInAppTz($ymd, $r->end_time)->setTimezone(new DateTimeZone("UTC")),
            ];
        }
        return $out;
    }

    public function get_staff_breaks(int $staff_id, string $ymd): array {
        $day = $this->dayEnumFromYmd($ymd);
        $wb = $this->db->prefixTable("weekly_breaks");

        $rows = $this->db->query("
            SELECT start_time, end_time, title
            FROM $wb
            WHERE deleted=0 AND is_active=1 AND staff_id=? AND day_of_week=?
        ", [$staff_id, $day])->getResult();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                "start" => $this->dtFromDateAndTimeInAppTz($ymd, $r->start_time)->setTimezone(new DateTimeZone("UTC")),
                "end"   => $this->dtFromDateAndTimeInAppTz($ymd, $r->end_time)->setTimezone(new DateTimeZone("UTC")),
                "title" => $r->title ?? null
            ];
        }
        return $out;
    }

    public function staff_has_conflict(int $staff_id, DateTime $slotStartUtc, DateTime $slotEndUtc): bool {
        $apps = $this->db->prefixTable("customer_appointments");

        $start = $slotStartUtc->format("Y-m-d H:i:s");
        $end   = $slotEndUtc->format("Y-m-d H:i:s");

        // overlap condition: existing.start < slotEnd AND existing.end > slotStart
        $row = $this->db->query("
            SELECT id FROM $apps
            WHERE deleted=0 AND staff_id=?
              AND status IN ('pending','confirmed')
              AND start_time < ?
              AND end_time > ?
            LIMIT 1
        ", [$staff_id, $end, $start])->getRow();

        return (bool)$row;
    }

    public function interval_overlaps(DateTime $aStart, DateTime $aEnd, DateTime $bStart, DateTime $bEnd): bool {
        return ($aStart < $bEnd) && ($aEnd > $bStart);
    }

    public function is_inside_any_window(DateTime $slotStart, DateTime $slotEnd, array $windows): bool {
        foreach ($windows as $w) {
            if ($slotStart >= $w["start"] && $slotEnd <= $w["end"]) return true;
        }
        return false;
    }

    public function overlaps_any_break(DateTime $slotStart, DateTime $slotEnd, array $breaks): bool {
        foreach ($breaks as $b) {
            if ($this->interval_overlaps($slotStart, $slotEnd, $b["start"], $b["end"])) return true;
        }
        return false;
    }

    /* =========================================================
     * Slot generation across staff (Option B scheduler)
     * - service slot interval + buffers + duration
     * - a slot is "available" if at least one staff is available
     * ========================================================= */

    public function get_available_slots_for_date(int $service_id, string $ymd, string $userTimezone): array {
        $service = $this->get_service($service_id);
        if (!$service) {
            return ["success" => false, "message" => "Invalid service."];
        }

        $duration = (int)$service->duration_minutes;
        $interval = (int)$service->slot_interval_minutes;
        $bufferBefore = (int)$service->buffer_before_minutes;
        $bufferAfter  = (int)$service->buffer_after_minutes;
        $minNotice    = (int)$service->min_notice_minutes;
        $maxAdvance   = (int)$service->max_advance_days;

        if ($duration <= 0 || $interval <= 0) {
            return ["success" => false, "message" => "Service is not configured properly."];
        }

        // max advance validation
        $today = new \DateTime("now", $this->get_app_timezone());
        $target = new \DateTime($ymd . " 00:00:00", $this->get_app_timezone());
        $diffDays = (int)floor(($target->getTimestamp() - $today->getTimestamp()) / 86400);
        if ($diffDays > $maxAdvance) {
            return ["success" => true, "slots" => [], "availability" => [], "breaks" => [], "timezone_label" => $userTimezone];
        }

        $staffIds = $this->get_active_staff_ids();
        if (!$staffIds) {
            return ["success" => true, "slots" => [], "availability" => [], "breaks" => [], "timezone_label" => $userTimezone];
        }

        // prefetch staff windows/breaks for the day (UTC)
        $staffData = [];
        foreach ($staffIds as $sid) {
            $wins = $this->get_staff_availability_windows($sid, $ymd);
            if (!$wins) continue;

            $brks = $this->get_staff_breaks($sid, $ymd);
            $staffData[$sid] = ["windows" => $wins, "breaks" => $brks];
        }

        if (!$staffData) {
            return ["success" => true, "slots" => [], "availability" => [], "breaks" => [], "timezone_label" => $userTimezone];
        }

        // build candidate time range from union of windows (coarse)
        // We'll iterate per staff window and build slots; then merge unique by start/end.
        $nowUtc = new DateTime("now", new DateTimeZone("UTC"));
        $minStartAllowedUtcTs = $nowUtc->getTimestamp() + ($minNotice * 60);

        $slotMap = []; // key => slot info
        foreach ($staffData as $sid => $sd) {
            foreach ($sd["windows"] as $w) {
                $startIter = clone $w["start"];
                $endLimit  = clone $w["end"];

                // step by interval, but ensure we can fit duration+buffers
                while (true) {
                    $slotStart = clone $startIter;

                    // apply buffers as part of required free time
                    $effectiveStart = (clone $slotStart);
                    $effectiveStart->modify("-{$bufferBefore} minutes");

                    $slotEnd = (clone $slotStart);
                    $slotEnd->modify("+{$duration} minutes");

                    $effectiveEnd = (clone $slotEnd);
                    $effectiveEnd->modify("+{$bufferAfter} minutes");

                    if ($effectiveEnd > $endLimit) break;

                    // min notice (start time must be after threshold)
                    if ($slotStart->getTimestamp() < $minStartAllowedUtcTs) {
                        $startIter->modify("+{$interval} minutes");
                        continue;
                    }

                    // inside window
                    if (!$this->is_inside_any_window($effectiveStart, $effectiveEnd, $sd["windows"])) {
                        $startIter->modify("+{$interval} minutes");
                        continue;
                    }

                    // not in breaks
                    if ($this->overlaps_any_break($effectiveStart, $effectiveEnd, $sd["breaks"])) {
                        $startIter->modify("+{$interval} minutes");
                        continue;
                    }

                    // no conflicts
                    if ($this->staff_has_conflict($sid, $effectiveStart, $effectiveEnd)) {
                        $startIter->modify("+{$interval} minutes");
                        continue;
                    }

                    $key = $slotStart->format("Y-m-d H:i:s") . "|" . $slotEnd->format("Y-m-d H:i:s");
                    if (!isset($slotMap[$key])) {
                        $slotMap[$key] = [
                            "startUtc" => clone $slotStart,
                            "endUtc"   => clone $slotEnd,
                            "staffCandidates" => [$sid]
                        ];
                    } else {
                        $slotMap[$key]["staffCandidates"][] = $sid;
                    }

                    $startIter->modify("+{$interval} minutes");
                }
            }
        }

        if (!$slotMap) {
            // also provide summary data (optional)
            return ["success" => true, "slots" => [], "availability" => [], "breaks" => [], "timezone_label" => $userTimezone];
        }

        // sort by start
        uasort($slotMap, function($a, $b) {
            return $a["startUtc"] <=> $b["startUtc"];
        });

        $slots = [];
        foreach ($slotMap as $k => $v) {
            $startUtc = $v["startUtc"];
            $endUtc   = $v["endUtc"];

            $slots[] = [
                "start_utc" => $this->toUtcIso($startUtc),
                "end_utc"   => $this->toUtcIso($endUtc),
                "display"   => $this->formatInTimezone($startUtc, $userTimezone, "h:i A") . " - " . $this->formatInTimezone($endUtc, $userTimezone, "h:i A"),
            ];
        }

        // Provide optional summary windows/breaks for UI (aggregate)
        $summaryAvail = [];
        $summaryBreaks = [];

        // show a compact summary based on app timezone for the day: first 5 staff windows/breaks
        $count = 0;
        foreach ($staffData as $sid => $sd) {
            if ($count >= 5) break;
            foreach ($sd["windows"] as $w) {
                $summaryAvail[] = [
                    "label" => "Staff #{$sid}",
                    "start" => $this->formatInTimezone($w["start"], $userTimezone, "h:i A"),
                    "end"   => $this->formatInTimezone($w["end"], $userTimezone, "h:i A"),
                ];
            }
            foreach ($sd["breaks"] as $b) {
                $summaryBreaks[] = [
                    "title" => $b["title"] ?: "Break",
                    "start" => $this->formatInTimezone($b["start"], $userTimezone, "h:i A"),
                    "end"   => $this->formatInTimezone($b["end"], $userTimezone, "h:i A"),
                ];
            }
            $count++;
        }

        return [
            "success" => true,
            "slots" => $slots,
            "availability" => $summaryAvail,
            "breaks" => $summaryBreaks,
            "timezone_label" => $userTimezone
        ];
    }

    public function get_available_dates_range(int $service_id, string $from, string $to, string $tz = "UTC"): array
{
    $services_table = $this->db->prefixTable("services");             // pcm_services
    $users_table    = $this->db->prefixTable("users");                // pcm_users
    $wa_table       = $this->db->prefixTable("weekly_availability");  // pcm_weekly_availability

    // 1) Service validation
    $service = $this->db->table($services_table)
        ->select("id, is_active, deleted, max_advance_days")
        ->where("id", $service_id)
        ->where("deleted", 0)
        ->get()
        ->getRow();

    if (!$service || (int)$service->is_active !== 1) {
        return [
            "success" => true,
            "available_dates" => [],
            "timezone_label" => $tz
        ];
    }

    // 2) Normalize range + apply max_advance_days
    $from_dt = date_create($from);
    $to_dt   = date_create($to);

    if (!$from_dt || !$to_dt) {
        throw new \RuntimeException("Invalid date range format. Expected YYYY-MM-DD.");
    }

    // max_advance_days guard
    $maxAdvance = (int)($service->max_advance_days ?? 365);
    if ($maxAdvance <= 0) $maxAdvance = 365;

    $today = new \DateTime("now");
    $maxTo = (clone $today)->modify("+{$maxAdvance} days");

    if ($to_dt > $maxTo) {
        $to_dt = $maxTo;
    }

    if ($to_dt < $from_dt) {
        return [
            "success" => true,
            "available_dates" => [],
            "timezone_label" => $tz
        ];
    }

    // 3) Build a set of "eligible staff" (active staff + admins)
    // NOTE: You can refine this later (e.g., only staff assigned to a service).
    $eligibleStaff = $this->db->table($users_table)
        ->select("id")
        ->where("deleted", 0)
        ->where("status", "active")
        ->groupStart()
            ->where("user_type", "staff")
            ->orWhere("is_admin", 1)
        ->groupEnd()
        ->get()
        ->getResultArray();

    $staffIds = array_map(fn($r) => (int)$r["id"], $eligibleStaff);
    if (!$staffIds) {
        // No staff/admin in system
        return [
            "success" => true,
            "available_dates" => [],
            "timezone_label" => $tz
        ];
    }

    // 4) Fetch availability rows once (for speed)
    $availRows = $this->db->table($wa_table)
        ->select("staff_id, day_of_week, start_time, end_time")
        ->where("deleted", 0)
        ->where("is_available", 1)
        ->whereIn("staff_id", $staffIds)
        ->get()
        ->getResultArray();

    if (!$availRows) {
        return [
            "success" => true,
            "available_dates" => [],
            "timezone_label" => $tz
        ];
    }

    // 5) Convert availability into a lookup by day_of_week (Mon/Tue/...)
    $availByDay = [];
    foreach ($availRows as $r) {
        $day = (string)$r["day_of_week"]; // enum Mon/Tue/...
        // basic validity (must have time window)
        if (!empty($r["start_time"]) && !empty($r["end_time"]) && $r["start_time"] < $r["end_time"]) {
            $availByDay[$day] = true;
        }
    }

    // 6) Iterate dates and include those matching a day with any availability
    $available_dates = [];
    $cursor = clone $from_dt;

    while ($cursor <= $to_dt) {
        $dow = $this->map_date_to_mon_tue_enum($cursor); // Mon/Tue/...
        if (isset($availByDay[$dow])) {
            $available_dates[] = $cursor->format("Y-m-d");
        }
        $cursor->modify("+1 day");
    }

    return [
        "success" => true,
        "available_dates" => $available_dates,
        "timezone_label" => $tz
    ];
}

private function map_date_to_mon_tue_enum(\DateTime $dt): string
{
    // PHP: Mon/Tue/Wed/Thu/Fri/Sat/Sun already matches your enum style
    return $dt->format("D"); // "Mon".."Sun"
}


    /* =========================================================
     * Round-robin staff pick (persistent)
     * ========================================================= */

    public function rr_get_last_staff_id(int $service_id): ?int {
        $st = $this->db->prefixTable("service_assignment_state");
        $row = $this->db->query("SELECT last_staff_id FROM $st WHERE service_id=?", [$service_id])->getRow();
        return $row ? (int)$row->last_staff_id : null;
    }

    public function rr_set_last_staff_id(int $service_id, int $staff_id): void {
        $st = $this->db->prefixTable("service_assignment_state");
        // Upsert
        $this->db->query("
            INSERT INTO $st (service_id, last_staff_id) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE last_staff_id=VALUES(last_staff_id)
        ", [$service_id, $staff_id]);
    }

    public function pick_staff_for_slot_round_robin(int $service_id, string $startUtcIso, string $endUtcIso): ?int {
        $service = $this->get_service($service_id);
        if (!$service) return null;

        $startUtc = $this->dtFromUtcIso($startUtcIso);
        $endUtc   = $this->dtFromUtcIso($endUtcIso);

        $ymd = $startUtc->format("Y-m-d");

        $staffIds = $this->get_active_staff_ids();
        if (!$staffIds) return null;

        // Determine eligible staff for that slot
        $eligible = [];
        foreach ($staffIds as $sid) {
            $wins = $this->get_staff_availability_windows($sid, $ymd);
            if (!$wins) continue;
            $brks = $this->get_staff_breaks($sid, $ymd);

            // include buffers as effective time block
            $effectiveStart = clone $startUtc;
            $effectiveEnd   = clone $endUtc;

            $bufferBefore = (int)$service->buffer_before_minutes;
            $bufferAfter  = (int)$service->buffer_after_minutes;

            if ($bufferBefore > 0) $effectiveStart->modify("-{$bufferBefore} minutes");
            if ($bufferAfter  > 0) $effectiveEnd->modify("+{$bufferAfter} minutes");

            if (!$this->is_inside_any_window($effectiveStart, $effectiveEnd, $wins)) continue;
            if ($this->overlaps_any_break($effectiveStart, $effectiveEnd, $brks)) continue;
            if ($this->staff_has_conflict($sid, $effectiveStart, $effectiveEnd)) continue;

            $eligible[] = $sid;
        }

        if (!$eligible) return null;

        // RR selection using persisted last_staff_id
        sort($eligible);
        $last = $this->rr_get_last_staff_id($service_id);

        if ($last === null) {
            $chosen = $eligible[0];
            $this->rr_set_last_staff_id($service_id, $chosen);
            return $chosen;
        }

        // find next after last
        $idx = array_search($last, $eligible, true);
        if ($idx === false) {
            $chosen = $eligible[0];
            $this->rr_set_last_staff_id($service_id, $chosen);
            return $chosen;
        }

        $next = $eligible[($idx + 1) % count($eligible)];
        $this->rr_set_last_staff_id($service_id, $next);
        return $next;
    }

    /* =========================================================
     * Stripe session persistence (model-level)
     * ========================================================= */

    public function save_stripe_session_id(int $appointment_id, string $session_id): void {
        $apps = $this->db->prefixTable("customer_appointments");
        // Column exists in your schema
        $this->db->query("UPDATE $apps SET stripe_session_id=? WHERE id=?", [$session_id, $appointment_id]);
    }

    public function mark_payment_paid(int $appointment_id): void {
        $apps = $this->db->prefixTable("customer_appointments");
        $this->db->query("UPDATE $apps SET payment_status='paid' WHERE id=?", [$appointment_id]);
    }

    public function mark_payment_unpaid(int $appointment_id): void {
        $apps = $this->db->prefixTable("customer_appointments");
        $this->db->query("UPDATE $apps SET payment_status='unpaid' WHERE id=?", [$appointment_id]);
    }

    /* =========================================================
     * Email notifications (simple + reliable)
     * ========================================================= */

    public function get_admin_email(): string {
        // If you have a setting for admin email, use it. Fallback to company email.
        $company = model("App\Models\Company_model")->get_one_where(["is_default" => true]);
        if ($company && !empty($company->email)) return $company->email;
        return "admin@" . parse_url(base_url(), PHP_URL_HOST);
    }
private function get_admin_emails(): array
{
    // Pull active admins from pcm_users
    $users = $this->db->prefixTable('users');
    $rows = $this->db->query("SELECT email FROM $users WHERE deleted=0 AND status='active' AND is_admin=1")->getResult();
    $emails = [];
    foreach ($rows as $r) {
        if (!empty($r->email)) $emails[] = $r->email;
    }
    return array_values(array_unique($emails));
}
    public function get_staff_email(int $staff_id): ?string {
        $users = $this->db->prefixTable("users");
        $row = $this->db->query("SELECT email FROM $users WHERE id=? AND deleted=0", [$staff_id])->getRow();
        return $row ? (string)$row->email : null;
    }

    public function send_appointment_emails(object $appointment, string $eventType = "created"): void {
        $parser = \Config\Services::parser();
        $tpl = model("App\Models\Email_templates_model")->get_final_template("new_appointment_confirmation");

        $company = model("App\Models\Company_model")->get_one_where(["is_default" => true]);

        $parser_data = [
            "SIGNATURE" => $tpl->signature,
            "CONTACT_FIRST_NAME" => $appointment->name,
            "COMPANY_NAME" => $company->name ?? "Company",
            "LOGO_URL" => get_logo_url(),
            "EMAIL" => $appointment->email,
            "START_DATE_TIME" => $appointment->start_time,
            "END_DATE_TIME" => $appointment->end_time,
            "DURATION" => $appointment->duration,
            "SERVICE_NAME" => $appointment->services_title ?? "Service",
            "MEETING_LINK" => $appointment->meeting_link ?? "",
            "STATUS" => $appointment->status ?? "pending",
            "PAYMENT_STATUS" => $appointment->payment_status ?? "unpaid",
        ];

        $message = $parser->setData($parser_data)->renderString($tpl->message);
        $subject = $parser->setData($parser_data)->renderString($tpl->subject);

        // customer
        send_app_mail($appointment->email, $subject, $message);

        // assigned staff
        if (!empty($appointment->staff_id)) {
            $staffEmail = $this->get_staff_email((int)$appointment->staff_id);
            if ($staffEmail) send_app_mail($staffEmail, $subject, $message);
        }

        // admin
        send_app_mail($this->get_admin_email(), $subject, $message);
    }

public function get_default_admin_id(): int
{
    $users = $this->db->prefixTable("users"); // if your table is pcm_users, adjust below

    // If your table is pcm_users (as you showed), use:
    // $users = $this->db->prefixTable("pcm_users");

    $sql = "SELECT id
            FROM $users
            WHERE deleted=0 AND status='active' AND is_admin=1
            ORDER BY id ASC
            LIMIT 1";

    $row = $this->db->query($sql)->getRow();
    return $row ? (int)$row->id : 0;
}

public function confirm_stripe_payment_and_mark_paid(int $appointment_id, string $session_id): bool
{
    try {
        require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';

        $payment_setting = model('App\Models\Payment_methods_model')->get_oneline_payment_method("stripe");
        $secretKey = $payment_setting->secret_key ?? null;
        if (!$secretKey) return false;

        \Stripe\Stripe::setApiKey($secretKey);

        $session = \Stripe\Checkout\Session::retrieve($session_id);

        if (!$session || ($session->payment_status ?? '') !== 'paid') {
            return false;
        }

        // Optional: verify appointment_id matches metadata
        $metaId = $session->metadata->appointment_id ?? null;
        if ($metaId && (int)$metaId !== $appointment_id) {
            return false;
        }

        // Update appointment
        $data = [
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
            'stripe_session_id' => $session_id,
        ];

        $this->ci_save($data, $appointment_id);
        return true;

    } catch (\Throwable $e) {
        log_message('error', 'Stripe confirm failed: ' . $e->getMessage());
        return false;
    }
}

}
