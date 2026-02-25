<?php

namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\ProjectModel;

class Reports extends BaseAppController
{
    /**
     * GET /reports — executive cross-project dashboard
     */
    public function index(): string
    {
        $rm      = new ReportModel();
        $summary = $rm->executiveSummary();

        return $this->render('reports/index', [
            'projects' => $summary['projects'],
            'totals'   => $summary['totals'],
            'taskKpi'  => $summary['taskKpi'],
        ]);
    }

    /**
     * GET /projects/:id/report — single project report
     */
    public function project(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $rm      = new ReportModel();
        $kpis    = $rm->projectSummary($projectId);

        return $this->render('reports/project', [
            'project' => $project,
            'kpis'    => $kpis,
        ]);
    }

    /**
     * GET /projects/:id/report/json — JSON KPI data for AJAX chart refresh
     */
    public function projectJson(int $projectId): \CodeIgniter\HTTP\Response
    {
        $kpis = (new ReportModel())->projectSummary($projectId);
        return $this->response->setJSON($kpis);
    }

    /**
     * GET /projects/:id/report/export — PDF-friendly print view
     */
    public function exportPrint(int $projectId): string
    {
        $project = (new ProjectModel())->find($projectId);
        $kpis    = (new ReportModel())->projectSummary($projectId);
        return $this->render('reports/print', [
            'project' => $project,
            'kpis'    => $kpis,
        ]);
    }

    /**
     * GET /reports/json — executive dashboard JSON (for chart polling)
     */
    public function executiveJson(): \CodeIgniter\HTTP\Response
    {
        $rm      = new ReportModel();
        $summary = $rm->executiveSummary();
        return $this->response->setJSON($summary);
    }
}
