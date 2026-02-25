<?php

namespace App\Models;

use CodeIgniter\Model;

class TimesheetEntryModel extends Model
{
    protected $table         = 'timesheet_entries';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'timesheet_id','project_id','task_id','cost_code_id','area_id',
        'entry_date','hours','description','is_billable',
    ];

    /**
     * All entries for a timesheet, with project title and task title.
     */
    public function forTimesheet(int $timesheetId): array
    {
        return $this->select('timesheet_entries.*, projects.title AS project_title, tasks.title AS task_title')
                    ->join('projects', 'projects.id = timesheet_entries.project_id', 'left')
                    ->join('tasks',    'tasks.id = timesheet_entries.task_id',    'left')
                    ->where('timesheet_entries.timesheet_id', $timesheetId)
                    ->orderBy('timesheet_entries.entry_date')
                    ->findAll();
    }

    /**
     * Hours summary grouped by project for a user in a date range.
     */
    public function summaryByProject(int $userId, string $from, string $to): array
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT p.title AS project_title, p.id AS project_id,
                   SUM(te.hours) AS total_hours
            FROM timesheet_entries te
            JOIN timesheets t ON t.id = te.timesheet_id
            JOIN projects   p ON p.id = te.project_id
            WHERE t.user_id = ? AND te.entry_date BETWEEN ? AND ?
            GROUP BY te.project_id
            ORDER BY total_hours DESC
        ", [$userId, $from, $to])->getResultArray();
    }
}
