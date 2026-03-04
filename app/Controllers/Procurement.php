<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\PurchaseOrderModel;
use App\Models\PoItemModel;
use App\Models\UserModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Procurement extends BaseAppController
{
    /**
     * POST /projects/:id/procurement/pos
     * Drafts a new Purchase Order
     */
    public function createPo(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $poModel = new PurchaseOrderModel();

        // 1. Generate next PO Number (e.g. PO-001)
        $count = $poModel->where('project_id', $projectId)->countAllResults();
        $nextNo = 'PO-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $poId = $poModel->insert([
            'project_id'    => $projectId,
            'vendor_id'     => $this->request->getPost('vendor_id') ?: null,
            'po_number'     => $nextNo,
            'title'         => $this->request->getPost('title') ?: 'Materials & Labor',
            'status'        => 'Draft',
            'delivery_date' => $this->request->getPost('delivery_date') ?: null,
            'notes'         => $this->request->getPost('notes'),
            'created_by'    => $this->currentUser['id']
        ]);

        return redirect()->to(site_url("procurement/pos/{$poId}"))->with('success', 'Draft Purchase Order created. Add line items below.');
    }

    /**
     * GET /procurement/pos/:id
     * The fillable worksheet view for a specific Purchase Order
     */
    public function showPo(int $id): string
    {
        $poModel   = new PurchaseOrderModel();
        $itemModel = new PoItemModel();
        
        $po = $poModel->find($id);
        if (!$po) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $project = (new ProjectModel())->find($po['project_id']);
        $items = $itemModel->forPo($id);

        $subcontractors = (new UserModel())->select('fs_users.*')
            ->join('user_roles', 'user_roles.user_id = fs_users.id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('roles.slug', 'subcontractor_vendor')
            ->findAll();

        return $this->render('procurement/po_show', [
            'project'        => $project,
            'po'             => $po,
            'items'          => $items,
            'subcontractors' => $subcontractors
        ]);
    }

    /**
     * POST /procurement/pos/:id/items
     * Upserts the PO line items and triggers status updates
     */
    public function savePoItems(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $poModel   = new PurchaseOrderModel();
        $itemModel = new PoItemModel();
        
        $po = $poModel->find($id);
        if (!$po) return redirect()->back()->with('error', 'Purchase Order not found.');

        // Wipe existing items and rebuild
        $itemModel->where('po_id', $id)->delete();

        $descriptions = $this->request->getPost('descriptions') ?? [];
        $quantities   = $this->request->getPost('quantities') ?? [];
        $units        = $this->request->getPost('units') ?? [];
        $unitPrices   = $this->request->getPost('unit_prices') ?? [];

        $insertItems = [];
        $totalAmount = 0.0;

        foreach ($descriptions as $index => $desc) {
            if (empty(trim((string)$desc))) continue;
            
            $qty   = isset($quantities[$index]) ? (float)$quantities[$index] : 0;
            $price = isset($unitPrices[$index]) ? (float)$unitPrices[$index] : 0;
            $total = $qty * $price;

            $totalAmount += $total;

            $insertItems[] = [
                'po_id'       => $id,
                'description' => $desc,
                'quantity'    => $qty,
                'unit'        => isset($units[$index]) ? $units[$index] : 'LS',
                'unit_price'  => $price,
                'total'       => $total
            ];
        }

        if (!empty($insertItems)) {
            $itemModel->insertBatch($insertItems);
        }

        // Update Master PO
        $updateData = [
            'total_amount'  => $totalAmount,
            'title'         => $this->request->getPost('title'),
            'vendor_id'     => $this->request->getPost('vendor_id') ?: null,
            'delivery_date' => $this->request->getPost('delivery_date') ?: null,
            'notes'         => $this->request->getPost('notes'),
        ];

        $action = $this->request->getPost('status_action');
        if ($action === 'send') {
            $updateData['status'] = 'Sent';
        } elseif ($action === 'execute') {
            $updateData['status'] = 'Executed';
        } elseif ($action === 'void') {
            $updateData['status'] = 'Void';
        }

        $poModel->update($id, $updateData);

        if (in_array($action, ['send', 'execute', 'void'])) {
            return redirect()->to(site_url("projects/{$po['project_id']}?tab=procurement"))->with('success', "Purchase Order marked as {$updateData['status']}.");
        }

        return redirect()->back()->with('success', 'Purchase Order saved.');
    }

    /**
     * GET /procurement/pos/:id/pdf
     * Generates the PO PDF
     */
    public function exportPoPdf(int $id)
    {
        $poModel   = new PurchaseOrderModel();
        $itemModel = new PoItemModel();
        
        // Use custom join method
        $poRecord = $poModel->forProject((new PurchaseOrderModel())->find($id)['project_id'] ?? 0);
        $po = null;
        foreach($poRecord as $r) { if($r['id'] == $id) $po = $r; }

        if (!$po) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $project = (new ProjectModel())->find($po['project_id']);
        $items = $itemModel->forPo($id);

        $html = view('procurement/po_pdf', [
            'project' => $project,
            'po'      => $po,
            'items'   => $items
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "{$project['title']} - {$po['po_number']}.pdf";
        $dompdf->stream(preg_replace('/[^A-Za-z0-9_\- ]/', '_', $filename), ["Attachment" => true]);
    }
}
