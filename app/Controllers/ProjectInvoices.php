<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjectInvoiceModel;
use App\Models\InvoiceItemModel;
use App\Models\ClientModel;
use App\Models\PaymentModel;

class ProjectInvoices extends BaseController
{
    protected $invoiceModel;
    protected $invoiceItemModel;
    protected $clientModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->invoiceModel = new ProjectInvoiceModel();
        $this->invoiceItemModel = new InvoiceItemModel();
        $this->clientModel = new ClientModel();
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Invoices',
            'invoices' => $this->invoiceModel
                ->select('project_invoices.*, clients.company_name')
                ->join('clients', 'clients.id = project_invoices.project_id', 'left') // Fixed: project_invoices uses project_id, not client_id
                ->findAll()
        ];
        return view('project_invoices/index', $data);
    }

    public function show($id)
    {
        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) {
            return redirect()->to(site_url('invoices'))->with('error', 'Invoice not found.');
        }

        $client = $this->clientModel->find($invoice['client_id']);
        $items = $this->invoiceItemModel->where('invoice_id', $id)->findAll();
        $payments = $this->paymentModel->where('invoice_id', $id)->findAll();

        return view('project_invoices/view', [
            'title' => 'Invoice #' . $id,
            'invoice' => $invoice,
            'client' => $client,
            'items' => $items,
            'payments' => $payments
        ]);
    }

    public function add_payment($id)
    {
        $amount = $this->request->getPost('amount');
        $date = $this->request->getPost('payment_date');
        $method = $this->request->getPost('payment_method_id'); // Placeholder for now

        if ($amount > 0) {
            $this->paymentModel->insert([
                'invoice_id' => $id,
                'amount' => $amount,
                'payment_date' => $date,
                'payment_method_id' => $method,
                'created_by' => session()->get('user_id')
            ]);

            // Update Invoice Status and Totals (Simplistic Logic)
            $invoice = $this->invoiceModel->find($id);
            $newPaid = $invoice['payment_received'] + $amount;
            
            // Calculate total (Should use a helper or model method)
            $items = $this->invoiceItemModel->where('invoice_id', $id)->findAll();
            $total = 0;
            foreach($items as $item) $total += $item['total'];

            $status = ($newPaid >= $total) ? 'fully_paid' : 'partially_paid';

            $this->invoiceModel->update($id, [
                'payment_received' => $newPaid,
                'status' => $status
            ]);
            
            return redirect()->to(site_url('invoices/' . $id))->with('message', 'Payment added successfully.');
        }
        
        return redirect()->back()->with('error', 'Invalid payment amount.');
    }
}
