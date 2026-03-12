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
            $driverId = ($row['driver_id'] ?? null) ?: null;
            $multiplier = (float)($row['driver_multiplier'] ?? 1.0);
            $qty = (float)($row['quantity'] ?? 0);

            // If linked to a driver, override qty with driver_value * multiplier
            if ($driverId) {
                $driver = (new \App\Models\QuantityDriverModel())->find($driverId);
                if ($driver) {
                    $qty = (float)$driver['value'] * $multiplier;
                }
            }

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
                'driver_id'   => $driverId,
                'driver_multiplier' => $multiplier
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

    /**
     * POST /projects/:id/boq/import
     * Trigger the Python helper to parse the master excel and import into BOQ.
     */
    public function import(int $projectId): \CodeIgniter\HTTP\Response
    {
        $filePath = 'c:\\wamp64\\www\\staging\\old_files\\Estimate_Master_SHARE_v1.xlsx';
        if (!file_exists($filePath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Master Excel file not found.']);
        }

        $pythonPath = 'python';
        $scriptPath = 'c:\\wamp64\\www\\staging\\app\\Helpers\\import_boq_helper.py';
        
        $command = "python \"$scriptPath\" \"$filePath\"";
        $output = shell_exec($command);
        
        $lines = explode("\n", trim($output));
        $jsonStr = end($lines); 
        $data = json_decode($jsonStr, true);

        if (!$data || isset($data['error'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to parse Excel: ' . ($data['error'] ?? 'Unknown error')]);
        }

        $boqModel = new BOQItemModel();
        $boqModel->where('project_id', $projectId)->delete();

        $codeToId = [];
        $importCount = 0;
        
        foreach ($data as $item) {
            $parentCode = $item['parent_code'] ?? null;
            $parent_id  = null;
            if ($parentCode && isset($codeToId[$parentCode])) {
                $parent_id = $codeToId[$parentCode];
            }

            $insertData = [
                'project_id'   => $projectId,
                'parent_id'    => $parent_id,
                'item_code'    => $item['item_code'] ?? null,
                'description'  => $item['description'] ?? '',
                'unit'         => $item['unit'] ?? null,
                'quantity'     => $item['quantity'] ?? 0,
                'unit_rate'    => $item['unit_rate'] ?? 0,
                'total_amount' => $item['total_amount'] ?? 0,
                'is_section'   => $item['is_section'] ?? 0,
                'sort_order'   => $importCount++,
            ];

            $newId = $boqModel->insert($insertData);
            $codeToId[$item['item_code']] = $newId;
        }

        return $this->response->setJSON(['success' => true, 'count' => $importCount]);
    }
}
