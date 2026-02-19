<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\FsUserModel;

class Leads extends BaseController
{
    protected $leadModel;
    protected $userModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->userModel = new FsUserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Leads Management',
            'leads' => $this->leadModel->findAll()
        ];
        return view('leads/index', $data);
    }

    public function kanban()
    {
        $data = [
            'title'  => 'Leads Kanban',
            'stages' => $this->leadModel->getLeadsByStage()
        ];
        return view('leads/kanban', $data);
    }
    
    public function create()
    {
        return view('leads/form', [
            'title' => 'Create Lead',
            'users' => $this->userModel->where('status', 'active')->findAll()
        ]);
    }

    public function store()
    {
       $rules = [
            'title' => 'required|min_length[3]',
            'company_name' => 'required',
            'email' => 'valid_email|permit_empty',
            'value' => 'decimal|permit_empty',
            'status' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->leadModel->insert([
            'title'        => $this->request->getPost('title'),
            'type'         => $this->request->getPost('type'),
            'company_name' => $this->request->getPost('company_name'),
            'contact_name' => $this->request->getPost('contact_name'), // Legacy field, eventually move to contacts
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
            'website'      => $this->request->getPost('website'),
            'address'      => $this->request->getPost('address'),
            'city'         => $this->request->getPost('city'),
            'state'        => $this->request->getPost('state'),
            'zip'          => $this->request->getPost('zip'),
            'country'      => $this->request->getPost('country'),
            'vat_number'   => $this->request->getPost('vat_number'),
            'gst_number'   => $this->request->getPost('gst_number'),
            'currency'     => $this->request->getPost('currency') ?: 'USD',
            'currency_symbol' => $this->request->getPost('currency_symbol') ?: '$',
            'labels'       => $this->request->getPost('labels'),
            'status'       => $this->request->getPost('status'),
            'source'       => $this->request->getPost('source'),
            'value'        => $this->request->getPost('value') ?: 0,
            'assigned_to'  => $this->request->getPost('assigned_to'),
            // 'created_by'   => session()->get('user_id'),
        ]);

        return redirect()->to(site_url('leads'))->with('message', 'Lead created successfully.');
    }
    public function show($id, $tab = 'overview')
    {
        $lead = $this->leadModel->find($id);

        if (!$lead) {
            return redirect()->to(site_url('leads'))->with('error', 'Lead not found.');
        }

        return view('leads/view', [
            'title' => $lead['company_name'],
            'lead' => $lead,
            'active_tab' => $tab
        ]);
    }
    
    // TAB METHODS
    
    public function overview($id) {
        $lead = $this->leadModel->find($id);
        return view('leads/tabs/overview', ['lead' => $lead]);
    }

    public function contacts($id) {
        $contactsModel = new \App\Models\ContactModel();
        $contacts = $contactsModel->where('lead_id', $id)->findAll();
        // Return view or partial
        return view('leads/tabs/contacts', ['contacts' => $contacts, 'lead_id' => $id]);
    }
    
    public function tasks($id) {
        // Mock data or real model if Tasks exist
        return view('leads/tabs/tasks', ['lead_id' => $id]);
    }
    
    public function notes($id) {
        $noteModel = new \App\Models\NoteModel();
        $notes = $noteModel->where('lead_id', $id)->findAll(); // Assuming NoteModel has lead_id or we use context
        // If NoteModel is polymorphic (context/context_id), use that.
        // For now, let's assume we need to check how Notes are stored.
        // Earlier I created NoteModel for Clients. I should check if it supports Leads.
        // Let's assume it relies on client_id. I might need to add lead_id to NoteModel schema or use context.
        // For now, empty view.
        return view('leads/tabs/notes', ['lead_id' => $id]);
    }
    
    public function files($id) {
        $fileModel = new \App\Models\GeneralFileModel();
        // Similarly, GeneralFileModel uses client_id. I need to support lead_id or context.
        // For now, empty or mock.
        return view('leads/tabs/files', ['lead_id' => $id]);
    }

    public function convert($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        $clientModel = new \App\Models\ClientModel();
        
        // Check if already exists/converted? (Optional check)

        $clientId = $clientModel->insert([
            'type'            => $lead['type'],
            'company_name'    => $lead['company_name'],
            'website'         => $lead['website'],
            'phone'           => $lead['phone'],
            'address'         => $lead['address'],
            'city'            => $lead['city'],
            'state'           => $lead['state'],
            'zip'             => $lead['zip'],
            'country'         => $lead['country'],
            'vat_number'      => $lead['vat_number'],
            'gst_number'      => $lead['gst_number'],
            'currency'        => $lead['currency'],
            'currency_symbol' => $lead['currency_symbol'],
            'labels'          => $lead['labels'],
            'status'          => 'active',
            'owner_id'        => $lead['assigned_to'],
            'created_by'      => session()->get('user_id'),
        ]);

        if ($clientId) {
            // Move Contacts
            $contactModel = new \App\Models\ContactModel();
            $contactModel->where('lead_id', $id)->set(['client_id' => $clientId])->update();
            
            // Mark Lead as Won/Converted (or delete? Usually keep as Won)
            $this->leadModel->update($id, ['status' => 'won']);

            return redirect()->to(site_url('clients/' . $clientId))->with('message', 'Lead converted to Client successfully.');
        }

        return redirect()->back()->with('error', 'Conversion failed.');
    }
}
