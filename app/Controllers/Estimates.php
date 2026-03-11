<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\ClientModel;
use App\Models\TaxModel;

class Estimates extends BaseAppController
{
    protected $estimateModel;
    protected $estimateItemModel;
    protected $clientModel;
    protected $taxModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
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

    public function clone($id)
    {
        $original = $this->estimateModel->find($id);
        if (!$original) return redirect()->back()->with('error', 'Estimate not found.');

        // Copy parent
        $cloneData = $original;
        unset($cloneData['id']);
        $cloneData['status'] = 'draft';
        $cloneData['estimate_date'] = date('Y-m-d');
        $cloneData['valid_until'] = date('Y-m-d', strtotime('+30 days'));
        $cloneData['public_key'] = bin2hex(random_bytes(16));
        $cloneId = $this->estimateModel->insert($cloneData);

        // Copy items
        $items = $this->estimateItemModel->where('estimate_id', $id)->findAll();
        foreach($items as $item) {
            $this->estimateItemModel->insert([
                'estimate_id' => $cloneId,
                'title' => $item['title'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'total' => $item['total']
            ]);
        }

        return redirect()->to(site_url('estimates/' . $cloneId))->with('success', 'Estimate cloned successfully.');
    }

    public function status($id)
    {
        $status = $this->request->getPost('status');
        if (in_array($status, ['draft', 'sent', 'accepted', 'declined'])) {
            $this->estimateModel->update($id, ['status' => $status]);
            
            if ($status === 'sent') {
                // Trigger Workflow for internal approval before sending to client?
                // Or just track it. Let's assume 'sent' implies it's gone through internal check.
                // In ERP, usually 'Approved' comes before 'Sent'.
                $items = $this->estimateItemModel->where('estimate_id', $id)->findAll();
                $totalVal = array_sum(array_column($items, 'total'));

                $workflow = new \App\Services\WorkflowEngine();
                $reqId = $workflow->submitRequest('estimates', 'estimate', $id, $this->currentUser['id'], [], session('branch_id'), (float)$totalVal);
                
                if (!$reqId) {
                    $this->estimateModel->update($id, ['status' => 'sent']); // Already sent or auto-approved
                } else {
                    $this->estimateModel->update($id, ['status' => 'pending_approval']);
                    return redirect()->back()->with('success', 'Estimate submitted for internal approval.');
                }
            }

            return redirect()->back()->with('success', 'Status updated to ' . ucfirst($status) . '.');
        }
        return redirect()->back()->with('error', 'Invalid status.');
    }

    public function send($id)
    {
        $estimate = $this->estimateModel->find($id);
        if (!$estimate) return redirect()->back()->with('error', 'Estimate not found.');

        // Dummy send logic for now
        $this->estimateModel->update($id, ['status' => 'sent']);
        return redirect()->back()->with('success', 'Estimate sent to client successfully.');
    }

    public function edit($id)
    {
        $estimate = $this->estimateModel->find($id);
        if (!$estimate) return redirect()->to(site_url('estimates'))->with('error', 'Estimate not found.');

        return view('estimates/form', [
            'title' => 'Edit Estimate #' . $id,
            'estimate' => $estimate,
            'clients' => $this->clientModel->where('status', 'active')->findAll(),
            'taxes' => $this->taxModel->findAll(),
            'items' => $this->estimateItemModel->where('estimate_id', $id)->findAll()
        ]);
    }

    public function addItem($id)
    {
        $estimate = $this->estimateModel->find($id);
        if (!$estimate) return redirect()->back()->with('error', 'Estimate not found.');

        $qty = (float)$this->request->getPost('quantity');
        $rate = (float)$this->request->getPost('rate');
        
        $this->estimateItemModel->insert([
            'estimate_id' => $id,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'quantity' => $qty,
            'rate' => $rate,
            'total' => $qty * $rate
        ]);

        return redirect()->back()->with('success', 'Item added.');
    }

    public function deleteItem($id, $itemId)
    {
        $item = $this->estimateItemModel->find($itemId);
        if ($item && $item['estimate_id'] == $id) {
            $this->estimateItemModel->delete($itemId);
            return redirect()->back()->with('success', 'Item deleted.');
        }
        return redirect()->back()->with('error', 'Item not found.');
    }

    public function delete($id)
    {
        $this->estimateModel->delete($id);
        return redirect()->to(site_url('estimates'))->with('success', 'Estimate deleted.');
    }

    public function pdf($id)
    {
        $estimate = $this->estimateModel->find($id);
        if (!$estimate) {
            return redirect()->to(site_url('estimates'))->with('error', 'Estimate not found.');
        }

        $client = $this->clientModel->find($estimate['client_id']);
        $items  = $this->estimateItemModel->where('estimate_id', $id)->findAll();

        $data = [
            'title'    => 'Estimate #' . $id,
            'estimate' => $estimate,
            'client'   => $client,
            'items'    => $items,
            'isPdf'    => true
        ];

        return view('estimates/print', $data);
    }
}
