<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarEventModel extends Model
{
    protected $table         = 'calendar_events';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'project_id','title','description','type','start_date','end_date',
        'all_day','location','color','assigned_to','created_by',
        'created_at','updated_at','deleted_at',
    ];

    /**
     * Get events between two dates (for FullCalendar JSON feed)
     */
    public function between(string $start, string $end, ?int $projectId = null): array
    {
        $b = $this->select('calendar_events.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS assignee_name, p.title AS project_name')
            ->join('fs_users', 'fs_users.id = calendar_events.assigned_to', 'left')
            ->join('projects p', 'p.id = calendar_events.project_id', 'left')
            ->where('calendar_events.deleted_at IS NULL', null, false)
            ->where('calendar_events.start_date <=', $end)
            ->groupStart()
                ->where('calendar_events.end_date >=', $start)
                ->orWhere('calendar_events.end_date IS NULL', null, false)
            ->groupEnd();

        if ($projectId) $b->where('calendar_events.project_id', $projectId);

        return $b->orderBy('start_date', 'ASC')->findAll();
    }

    /**
     * Aggregate tasks + milestones + events into unified calendar feed.
     */
    public function unifiedFeed(string $start, string $end, ?int $projectId = null): array
    {
        $db    = \Config\Database::connect();
        $items = [];

        // 1) Custom events
        foreach ($this->between($start, $end, $projectId) as $e) {
            $items[] = [
                'id'          => 'evt_' . $e['id'],
                'title'       => $e['title'],
                'start'       => $e['start_date'],
                'end'         => $e['end_date'] ?: $e['start_date'],
                'allDay'      => (bool)$e['all_day'],
                'color'       => $e['color'] ?? '#3b82f6',
                'type'        => $e['type'],
                'description' => $e['description'] ?? '',
                'project'     => $e['project_name'] ?? '',
                'location'    => $e['location'] ?? '',
                'source'      => 'event',
                'raw_id'      => (int)$e['id'],
            ];
        }

        // 2) Tasks with due dates
        $tq = $db->table('tasks t')
            ->select('t.id, t.title, t.due_date, t.status, t.priority, p.title AS project_name, t.project_id')
            ->join('projects p', 'p.id = t.project_id', 'left')
            ->where('t.due_date IS NOT NULL', null, false)
            ->where('t.due_date >=', $start)
            ->where('t.due_date <=', $end)
            ->where('t.deleted_at IS NULL', null, false);
        if ($projectId) $tq->where('t.project_id', $projectId);

        foreach ($tq->get()->getResultArray() as $t) {
            $color = match($t['status'] ?? '') {
                'done'        => '#22c55e',
                'in_progress' => '#f59e0b',
                'overdue'     => '#ef4444',
                default       => '#6366f1',
            };
            $items[] = [
                'id'      => 'task_' . $t['id'],
                'title'   => '📋 ' . $t['title'],
                'start'   => $t['due_date'],
                'allDay'  => true,
                'color'   => $color,
                'type'    => 'task',
                'project' => $t['project_name'] ?? '',
                'source'  => 'task',
                'raw_id'  => (int)$t['id'],
                'url'     => site_url("projects/{$t['project_id']}") . '?tab=tasks',
            ];
        }

        // 3) Milestones
        $mq = $db->table('project_milestones m')
            ->select('m.id, m.title, m.due_date, m.status, p.title AS project_name, m.project_id')
            ->join('projects p', 'p.id = m.project_id', 'left')
            ->where('m.due_date IS NOT NULL', null, false)
            ->where('m.due_date >=', $start)
            ->where('m.due_date <=', $end);
        if ($projectId) $mq->where('m.project_id', $projectId);

        foreach ($mq->get()->getResultArray() as $m) {
            $items[] = [
                'id'      => 'ms_' . $m['id'],
                'title'   => '🏁 ' . $m['title'],
                'start'   => $m['due_date'],
                'allDay'  => true,
                'color'   => ($m['status'] ?? '') === 'completed' ? '#22c55e' : '#8b5cf6',
                'type'    => 'milestone',
                'project' => $m['project_name'] ?? '',
                'source'  => 'milestone',
                'raw_id'  => (int)$m['id'],
                'url'     => site_url("projects/{$m['project_id']}") . '?tab=milestones',
            ];
        }

        return $items;
    }
}
