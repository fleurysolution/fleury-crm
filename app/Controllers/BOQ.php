<?php

namespace App\Controllers;

use App\Models\BOQItemModel;
use App\Models\ProjectModel;

class BOQ extends BaseAppController
{
    /**
     * GET /projects/:id/boq — BOQ spreadsheet-style view
     */
    public function index(int $projectId): string
    {
        $project  = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $boqModel = new BOQItemModel();
        $tree     = $boqModel->buildTree($projectId);
        $totalBOQ = $boqModel->totalBOQ($projectId);
        $totalAct = $boqModel->totalActual($projectId);

        return $this->render('boq/index', [
            'project'  => $project,
            'tree'     => $tree,
            'totalBOQ' => $totalBOQ,
            'totalAct' => $totalAct,
        ]);
    }

    /**
     * POST /projects/:id/boq — batch upsert BOQ rows (AJAX from spreadsheet)
     */
    public function save(int $projectId): \CodeIgniter\HTTP\Response
    {
        $rows     = $this->request->getJSON(true)['rows'] ?? [];
        $boqModel = new BOQItemModel();

        foreach ($rows as $row) {
            $qty    = (float)($row['quantity'] ?? 0);
            $rate   = (float)($row['unit_rate'] ?? 0);
            $data   = [
                'project_id'  => $projectId,
                'parent_id'   => ($row['parent_id'] ?? null) ?: null,
                'item_code'   => $row['item_code']   ?? null,
                'description' => $row['description'] ?? '',
                'unit'        => $row['unit']         ?? null,
                'quantity'    => $qty,
                'unit_rate'   => $rate,
                'total_amount'=> $qty * $rate,
                'is_section'  => (int)($row['is_section'] ?? 0),
                'sort_order'  => (int)($row['sort_order'] ?? 0),
            ];

            if (!empty($row['id'])) {
                $boqModel->update((int)$row['id'], $data);
            } else {
                $boqModel->insert($data);
            }
        }

        $boqModel2 = new BOQItemModel();
        return $this->response->setJSON([
            'success'  => true,
            'totalBOQ' => $boqModel2->totalBOQ($projectId),
            'totalAct' => $boqModel2->totalActual($projectId),
        ]);
    }

    /**
     * POST /boq/:id/delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\Response
    {
        (new BOQItemModel())->delete($id);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * GET /projects/:id/boq/export — CSV export of BOQ
     */
    public function exportCsv(int $projectId): void
    {
        $boqModel = new BOQItemModel();
        $items    = $boqModel->forProject($projectId);
        $project  = (new ProjectModel())->find($projectId);
        $filename = 'BOQ-' . preg_replace('/\s+/', '-', $project['title'] ?? $projectId) . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Code','Description','Unit','Qty','Unit Rate','Total (BOQ)','Actual Qty','Actual Amount','Variance']);
        foreach ($items as $it) {
            if ($it['is_section']) {
                fputcsv($out, ['', strtoupper($it['description']), '', '', '', '', '', '', '']);
                continue;
            }
            fputcsv($out, [
                $it['item_code'] ?? '',
                $it['description'],
                $it['unit'] ?? '',
                $it['quantity'],
                $it['unit_rate'],
                $it['total_amount'],
                $it['actual_qty'],
                $it['actual_amount'],
                round($it['actual_amount'] - $it['total_amount'], 2),
            ]);
        }
        fclose($out);
        exit;
    }
}
