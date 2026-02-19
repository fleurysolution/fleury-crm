<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\ClientModel;
use App\Models\TaxModel;

class Estimates extends BaseController
{
    protected $estimateModel;
    protected $estimateItemModel;
    protected $clientModel;
    protected $taxModel;

    public function __construct()
    {
        $this->estimateModel = new EstimateModel();
        $this->estimateItemModel = new EstimateItemModel();
        $this->clientModel = new ClientModel();
        $this->taxModel = new TaxModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Estimates',
            'estimates' => $this->estimateModel
                ->select('estimates.*, clients.company_name')
                ->join('clients', 'clients.id = estimates.client_id')
                ->findAll()
        ];
        return view('estimates/index', $data);
    }

    public function create()
    {
        return view('estimates/form', [
            'title' => 'Create Estimate',
            'clients' => $this->clientModel->where('status', 'active')->findAll(),
            'taxes' => $this->taxModel->findAll()
        ]);
    }

    public function store()
    {
        $rules = [
            'client_id' => 'required|numeric',
            'estimate_date' => 'required|valid_date',
            'valid_until' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate public key
        $publicKey = bin2hex(random_bytes(16));

        $estimateData = [
            'client_id' => $this->request->getPost('client_id'),
            'estimate_date' => $this->request->getPost('estimate_date'),
            'valid_until' => $this->request->getPost('valid_until'),
            'note' => $this->request->getPost('note'),
            'status' => 'draft',
            'currency' => 'USD', // Default for now
            'currency_symbol' => '$',
            'public_key' => $publicKey,
            'created_by' => session()->get('user_id'),
        ];

        $estimateId = $this->estimateModel->insert($estimateData);

        if ($estimateId) {
            // Process Items
            $items = $this->request->getPost('items');
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                     if (!empty($item['title'])) {
                        $this->estimateItemModel->insert([
                            'estimate_id' => $estimateId,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? '',
                            'quantity' => $item['quantity'] ?? 1,
                            'rate' => $item['rate'] ?? 0,
                            'total' => ($item['quantity'] ?? 1) * ($item['rate'] ?? 0)
                        ]);
                     }
                }
            }
            return redirect()->to(site_url('estimates/' . $estimateId))->with('message', 'Estimate created successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create estimate.');
    }

    public function show($id)
    {
        $estimate = $this->estimateModel->find($id);
        if (!$estimate) {
            return redirect()->to(site_url('estimates'))->with('error', 'Estimate not found.');
        }

        $client = $this->clientModel->find($estimate['client_id']);
        $items = $this->estimateItemModel->where('estimate_id', $id)->findAll();

        return view('estimates/view', [
            'title' => 'Estimate #' . $id,
            'estimate' => $estimate,
            'client' => $client,
            'items' => $items
        ]);
    }

    public function convert_to_invoice($id)
    {
        $estimate = $this->estimateModel->find($id);
        if (!$estimate) {
            return redirect()->back()->with('error', 'Estimate not found.');
        }

        $invoiceModel = new \App\Models\InvoiceModel();
        $invoiceItemModel = new \App\Models\InvoiceItemModel();

        // Create Invoice
        $invoiceId = $invoiceModel->insert([
            'client_id' => $estimate['client_id'],
            'bill_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'not_paid',
            'note' => $estimate['note'],
            'created_by' => session()->get('user_id')
        ]);

        if ($invoiceId) {
            // Copy Items
            $items = $this->estimateItemModel->where('estimate_id', $id)->findAll();
            foreach ($items as $item) {
                $invoiceItemModel->insert([
                    'invoice_id' => $invoiceId,
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'total' => $item['total']
                ]);
            }
            
            // Update Estimate Status
            $this->estimateModel->update($id, ['status' => 'accepted']);

            return redirect()->to(site_url('invoices/' . $invoiceId))->with('message', 'Converted to Invoice successfully.');
        }

        return redirect()->back()->with('error', 'Conversion failed.');
    }
}
