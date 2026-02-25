<?php

namespace App\Models;

use CodeIgniter\Model;

class TimesheetModel extends Model
{
    protected $table         = 'timesheets';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id','week_start','status','notes',
        'submitted_at','approved_by','approved_at','rejected_reason',
    ];

    /**
     * Get timesheets for a user with some joined fields.
     */
    public function forUser(int $userId): static
    {
        return $this->where('timesheets.user_id', $userId);
    }

    /**
     * Pending timesheets awaiting approval (for PM/Finance view).
     */
    public function pending(): static
    {
        return $this->where('status', 'submitted');
    }

    /**
     * Load timesheet with owner name.
     */
    public function withUserName(): static
    {
        return $this->select('timesheets.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS user_name, fs_users.email AS user_email')
                    ->join('fs_users', 'fs_users.id = timesheets.user_id', 'left');
    }

    /**
     * Total hours logged across entries for a timesheet.
     */
    public function totalHours(int $timesheetId): float
    {
        $db  = \Config\Database::connect();
        $row = $db->query('SELECT COALESCE(SUM(hours),0) AS total FROM timesheet_entries WHERE timesheet_id = ?', [$timesheetId])->getRow();
        return (float)($row->total ?? 0);
    }
}
