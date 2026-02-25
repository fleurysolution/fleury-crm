<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table          = 'saved_reports';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['project_id','title','type','filters','created_by'];

    // ── Project KPI snapshot (live, no caching) ───────────────────────────────

    public function projectSummary(int $projectId): array
    {
        $db = \Config\Database::connect();

        // Tasks
        $tasks = $db->query(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status="done" THEN 1 ELSE 0 END) AS done,
                SUM(CASE WHEN status="in_progress" THEN 1 ELSE 0 END) AS in_progress,
                SUM(CASE WHEN status="todo" THEN 1 ELSE 0 END) AS todo,
                SUM(CASE WHEN due_date < CURDATE() AND status!="done" THEN 1 ELSE 0 END) AS overdue
             FROM tasks WHERE project_id=? AND deleted_at IS NULL',
            [$projectId]
        )->getRow('array');

        // BOQ
        $boq = $db->query(
            'SELECT COALESCE(SUM(total_amount),0) AS budget, COALESCE(SUM(actual_amount),0) AS actual
             FROM boq_items WHERE project_id=? AND is_section=0 AND deleted_at IS NULL',
            [$projectId]
        )->getRow('array');

        // RFIs
        $rfis = $db->query(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status IN ("submitted","under_review") THEN 1 ELSE 0 END) AS open,
                SUM(CASE WHEN status="answered" THEN 1 ELSE 0 END) AS answered,
                SUM(CASE WHEN status="closed" THEN 1 ELSE 0 END) AS closed
             FROM rfis WHERE project_id=? AND deleted_at IS NULL',
            [$projectId]
        )->getRow('array');

        // Punch list
        $punch = $db->query(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status IN ("open","in_progress") THEN 1 ELSE 0 END) AS open,
                SUM(CASE WHEN status="resolved" THEN 1 ELSE 0 END) AS resolved,
                SUM(CASE WHEN status="closed" THEN 1 ELSE 0 END) AS closed
             FROM punch_list_items WHERE project_id=? AND deleted_at IS NULL',
            [$projectId]
        )->getRow('array');

        // Milestones
        $milestones = $db->query(
            'SELECT COUNT(*) AS total,
                SUM(CASE WHEN status="completed" THEN 1 ELSE 0 END) AS done
             FROM project_milestones WHERE project_id=?',
            [$projectId]
        )->getRow('array');

        // Site diary entries past 30 days
        $diary = $db->query(
            'SELECT COUNT(*) AS cnt FROM site_diary_entries WHERE project_id=? AND entry_date >= DATE_SUB(CURDATE(),INTERVAL 30 DAY) AND deleted_at IS NULL',
            [$projectId]
        )->getRow('array');

        // Payment certs
        $certs = $db->query(
            'SELECT COALESCE(SUM(CASE WHEN status="paid" THEN net_amount ELSE 0 END),0) AS paid,
                    COALESCE(SUM(net_amount),0) AS total
             FROM payment_certificates WHERE project_id=? AND deleted_at IS NULL',
            [$projectId]
        )->getRow('array');

        return [
            'tasks'      => $tasks,
            'boq'        => $boq,
            'rfis'       => $rfis,
            'punch'      => $punch,
            'milestones' => $milestones,
            'diary_30d'  => (int)($diary['cnt'] ?? 0),
            'certs'      => $certs,
        ];
    }

    // ── Executive cross-project summary ──────────────────────────────────────

    public function executiveSummary(): array
    {
        $db = \Config\Database::connect();

        $projects = $db->query(
            'SELECT p.id, p.title, p.status, p.color, p.budget,
                    (SELECT COUNT(*) FROM tasks WHERE project_id=p.id AND status="done" AND deleted_at IS NULL) AS tasks_done,
                    (SELECT COUNT(*) FROM tasks WHERE project_id=p.id AND deleted_at IS NULL) AS tasks_total,
                    (SELECT COALESCE(SUM(actual_amount),0) FROM boq_items WHERE project_id=p.id AND is_section=0 AND deleted_at IS NULL) AS cost_actual,
                    (SELECT COUNT(*) FROM rfis WHERE project_id=p.id AND status IN ("submitted","under_review") AND deleted_at IS NULL) AS rfis_open,
                    (SELECT COUNT(*) FROM punch_list_items WHERE project_id=p.id AND status IN ("open","in_progress") AND deleted_at IS NULL) AS punch_open
             FROM projects p WHERE p.deleted_at IS NULL AND p.status NOT IN ("archived","completed")
             ORDER BY p.created_at DESC',
            []
        )->getResultArray();

        $totals = $db->query(
            'SELECT
                COUNT(*) AS total_projects,
                SUM(CASE WHEN status="active" THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN status="on_hold" THEN 1 ELSE 0 END) AS on_hold,
                COALESCE(SUM(budget),0) AS total_budget
             FROM projects WHERE deleted_at IS NULL AND status NOT IN ("archived")',
            []
        )->getRow('array');

        $taskKpi = $db->query(
            'SELECT COUNT(*) AS total,
                SUM(CASE WHEN status="done" THEN 1 ELSE 0 END) AS done,
                SUM(CASE WHEN due_date < CURDATE() AND status!="done" THEN 1 ELSE 0 END) AS overdue
             FROM tasks WHERE deleted_at IS NULL',
            []
        )->getRow('array');

        return ['projects' => $projects, 'totals' => $totals, 'taskKpi' => $taskKpi];
    }
}
