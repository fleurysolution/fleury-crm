<?php

namespace App\Controllers;

use App\Models\PaymentCertificateModel;
use App\Models\InvoiceModel;
use App\Models\ProjectExpenseModel;
use App\Models\BOQItemModel;
use App\Models\ProjectModel;

class Finance extends BaseAppController
{
    /**
     * GET /projects/:id/finance — finance dashboard (IPCs + invoices + expenses)
     */
    public function index(int $projectId): string
    {
        $project    = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $certModel  = new PaymentCertificateModel();
        $certs      = $certModel->forProject($projectId);
        $totalPaid  = $certModel->totalPaid($projectId);

        $invModel   = new InvoiceModel();
        $invoices   = $invModel->forProject($projectId);
        $invTotals  = $invModel->totalByDirection($projectId);

        $expModel   = new ProjectExpenseModel();
        $expenses   = $expModel->forProject($projectId);
        $expTotal   = $expModel->totalApproved($projectId);
        $expByCat   = $expModel->totalByCategory($projectId);

        $boqModel   = new BOQItemModel();
        $totalBOQ   = $boqModel->totalBOQ($projectId);

        return $this->render('finance/index', [
            'project'   => $project,
            'certs'     => $certs,
            'totalPaid' => $totalPaid,
            'invoices'  => $invoices,
            'invTotals' => $invTotals,
            'expenses'  => $expenses,
            'expTotal'  => $expTotal,
            'expByCat'  => $expByCat,
            'totalBOQ'  => $totalBOQ,
        ]);
    }

    // ── Payment Certificates ──────────────────────────────────────────────────

    /**
     * POST /projects/:id/finance/certs — create IPC
     */
    public function storeCert(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $certModel = new PaymentCertificateModel();
        $gross     = (float)$this->request->getPost('gross_amount');
        $retPct    = (float)($this->request->getPost('retention_pct') ?: 10);
        $ret       = round($gross * $retPct / 100, 2);
        $net       = $gross - $ret;

        $id = $certModel->insert([
            'project_id'       => $projectId,
            'cert_number'      => $certModel->nextNumber($projectId),
            'period_from'      => $this->request->getPost('period_from') ?: null,
            'period_to'        => $this->request->getPost('period_to')   ?: null,
            'gross_amount'     => $gross,
            'retention_amount' => $ret,
            'net_amount'       => $net,
            'status'           => 'submitted',
            'submitted_by'     => $this->currentUser['id'],
            'notes'            => $this->request->getPost('notes'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'id' => $id]);
        }
        return redirect()->back()->with('success', 'Payment certificate created.');
    }

    /**
     * POST /finance/certs/:id/approve
     */
    public function approveCert(int $id): \CodeIgniter\HTTP\Response
    {
        (new PaymentCertificateModel())->update($id, [
            'status'      => 'approved',
            'approved_by' => $this->currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /finance/certs/:id/mark-paid
     */
    public function markCertPaid(int $id): \CodeIgniter\HTTP\Response
    {
        (new PaymentCertificateModel())->update($id, [
            'status'  => 'paid',
            'paid_at' => date('Y-m-d'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    // ── Invoices ──────────────────────────────────────────────────────────────

    /**
     * POST /projects/:id/finance/invoices — create invoice
     */
    public function storeInvoice(int $projectId): \CodeIgniter\HTTP\Response
    {
        $invModel = new InvoiceModel();
        $subtotal = (float)$this->request->getPost('subtotal');
        $tax      = (float)($this->request->getPost('tax_amount') ?: 0);

        $id = $invModel->insert([
            'project_id'     => $projectId,
            'invoice_number' => $invModel->nextNumber($projectId),
            'direction'      => $this->request->getPost('direction') ?: 'income',
            'party_name'     => $this->request->getPost('party_name'),
            'invoice_date'   => $this->request->getPost('invoice_date') ?: date('Y-m-d'),
            'due_date'       => $this->request->getPost('due_date') ?: null,
            'subtotal'       => $subtotal,
            'tax_amount'     => $tax,
            'total_amount'   => $subtotal + $tax,
            'status'         => 'sent',
            'notes'          => $this->request->getPost('notes'),
            'created_by'     => $this->currentUser['id'],
        ]);

        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    /**
     * POST /finance/invoices/:id/mark-paid — record payment
     */
    public function markInvoicePaid(int $id): \CodeIgniter\HTTP\Response
    {
        $invModel   = new InvoiceModel();
        $invoice    = $invModel->find($id);
        $paidAmount = (float)$this->request->getPost('paid_amount') ?: $invoice['total_amount'];
        $status     = $paidAmount >= $invoice['total_amount'] ? 'paid' : 'partial';
        $invModel->update($id, ['paid_amount' => $paidAmount, 'status' => $status]);
        return $this->response->setJSON(['success' => true, 'status' => $status]);
    }

    // ── Expenses ─────────────────────────────────────────────────────────────

    /**
     * POST /projects/:id/finance/expenses — create expense
     */
    public function storeExpense(int $projectId): \CodeIgniter\HTTP\Response
    {
        $id = (new ProjectExpenseModel())->insert([
            'project_id'   => $projectId,
            'category'     => $this->request->getPost('category'),
            'description'  => $this->request->getPost('description'),
            'amount'       => (float)$this->request->getPost('amount'),
            'expense_date' => $this->request->getPost('expense_date') ?: date('Y-m-d'),
            'vendor'       => $this->request->getPost('vendor'),
            'status'       => 'pending',
            'submitted_by' => $this->currentUser['id'],
        ]);
        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    /**
     * POST /finance/expenses/:id/approve
     */
    public function approveExpense(int $id): \CodeIgniter\HTTP\Response
    {
        (new ProjectExpenseModel())->update($id, [
            'status'      => 'approved',
            'approved_by' => $this->currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * GET /projects/:id/finance/export — cost report CSV
     */
    public function exportCsv(int $projectId): void
    {
        $project  = (new ProjectModel())->find($projectId);
        $expenses = (new ProjectExpenseModel())->forProject($projectId, 'approved');
        $invoices = (new InvoiceModel())->forProject($projectId);
        $certs    = (new PaymentCertificateModel())->forProject($projectId);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Finance-Report.csv"');
        $out = fopen('php://output', 'w');

        fputcsv($out, ['=== PAYMENT CERTIFICATES ===']);
        fputcsv($out, ['Number','Period','Gross','Retention','Net','Status','Paid On']);
        foreach ($certs as $c) {
            fputcsv($out, [$c['cert_number'], $c['period_from'].' — '.$c['period_to'], $c['gross_amount'], $c['retention_amount'], $c['net_amount'], $c['status'], $c['paid_at'] ?? '']);
        }

        fputcsv($out, []);
        fputcsv($out, ['=== INVOICES ===']);
        fputcsv($out, ['Number','Direction','Party','Date','Total','Paid','Status']);
        foreach ($invoices as $inv) {
            fputcsv($out, [$inv['invoice_number'], $inv['direction'], $inv['party_name'] ?? '', $inv['invoice_date'], $inv['total_amount'], $inv['paid_amount'], $inv['status']]);
        }

        fputcsv($out, []);
        fputcsv($out, ['=== EXPENSES ===']);
        fputcsv($out, ['Date','Category','Description','Vendor','Amount']);
        foreach ($expenses as $ex) {
            fputcsv($out, [$ex['expense_date'], $ex['category'] ?? '', $ex['description'], $ex['vendor'] ?? '', $ex['amount']]);
        }
        fclose($out);
        exit;
    }
}
