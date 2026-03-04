<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\ContactModel;
use App\Models\ProjectModel;
use App\Models\InvoiceModel;
use App\Models\EstimateModel;
use App\Models\ContractModel;
use App\Models\TicketModel;
use App\Models\ProposalModel;
use App\Models\GeneralFileModel;
use App\Models\NoteModel;
use App\Models\EventModel;
use App\Models\ExpenseModel;


class Clients extends BaseController
{
    protected $clientModel;
    protected $contactModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->contactModel = new ContactModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Clients',
            'clients' => $this->clientModel->findAll()
        ];
        return view('clients/index', $data);
    }

    public function show($id, $tab = 'overview')
    {
        $client = $this->clientModel->getDetails($id);

        if (!$client) {
            return redirect()->to(site_url('clients'))->with('error', 'Client not found.');
        }

        return view('clients/view', [
            'title' => $client['company_name'],
            'client' => $client,
            'active_tab' => $tab
        ]);
    }
    
    // TAB METHODS (Return Partial Views)
    
    public function overview($id) {
        $client = $this->clientModel->getDetails($id);
        return view('clients/tabs/overview', ['client' => $client]);
    }

    public function contacts($id) {
        $contacts = $this->contactModel->where('client_id', $id)->findAll();
        return view('clients/tabs/contacts', ['contacts' => $contacts, 'client_id' => $id]);
    }
    
    public function projects($id) {
        $model = new ProjectModel();
        $projects = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/projects', ['projects' => $projects, 'client_id' => $id]);
    }

    public function invoices($id) {
        $model = new InvoiceModel();
        $invoices = $model->select('project_invoices.*, projects.title as project_name')
                          ->join('projects', 'projects.id = project_invoices.project_id')
                          ->where('projects.client_id', $id)
                          ->findAll();
        return view('clients/tabs/invoices', ['invoices' => $invoices, 'client_id' => $id]);
    }

    public function estimates($id) {
        $model = new EstimateModel();
        $estimates = $model->select('project_estimates.*, projects.title as project_name')
                           ->join('projects', 'projects.id = project_estimates.project_id')
                           ->where('projects.client_id', $id)
                           ->findAll();
        return view('clients/tabs/estimates', ['estimates' => $estimates, 'client_id' => $id]);
    }
    
    public function contracts($id) {
        $model = new ContractModel();
        $contracts = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/contracts', ['contracts' => $contracts, 'client_id' => $id]);
    }
    
    public function proposals($id) {
        $model = new ProposalModel();
        $proposals = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/proposals', ['proposals' => $proposals, 'client_id' => $id]);
    }
    
    public function tickets($id) {
        $model = new TicketModel();
        $tickets = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/tickets', ['tickets' => $tickets, 'client_id' => $id]);
    }
    
    public function files($id) {
        $model = new GeneralFileModel();
        $files = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/files', ['files' => $files, 'client_id' => $id]);
    }

     public function notes($id) {
        $model = new NoteModel();
        $notes = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/notes', ['notes' => $notes, 'client_id' => $id]);
    }

    public function expenses($id) {
        $model = new ExpenseModel();
        $expenses = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/expenses', ['expenses' => $expenses, 'client_id' => $id]);
    }

    public function events($id) {
        $model = new EventModel();
        $events = $model->where('client_id', $id)->findAll();
        return view('clients/tabs/events', ['events' => $events, 'client_id' => $id]);
    }
    

    // CRUD METHODS

    public function modal_form()
    {
        $id = $this->request->getPost('id');
        $view_data['model_info'] = (object)['id' => $id];
        
        if ($id) {
            $view_data['model_info'] = (object)$this->clientModel->find($id);
        }

        // Mock Dropdowns for now
        $view_data['currency_dropdown'] = [
            ['id' => 'USD', 'text' => 'USD'],
            ['id' => 'EUR', 'text' => 'EUR'],
            ['id' => 'GBP', 'text' => 'GBP']
        ];
        
        // Groups Dropdown (Empty for now until model exists)
        $view_data['groups_dropdown'] = [];

        return view('clients/modal_form', $view_data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'company_name' => 'required',
            'account_type' => 'required'
        ];

        if (!$this->validate($rules)) {
            return json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'type' => $this->request->getPost('account_type'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'zip' => $this->request->getPost('zip'),
            'country' => $this->request->getPost('country'),
            'phone' => $this->request->getPost('phone'),
            'website' => $this->request->getPost('website'),
            'vat_number' => $this->request->getPost('vat_number'),
            'currency' => $this->request->getPost('currency'),
        ];
        
        if (!$id) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['status'] = 'active'; // Default status
        }

        if ($this->clientModel->save(array_merge($data, ['id' => $id]))) {
             $save_id = $id ? $id : $this->clientModel->getInsertID();
             // Return simplified data for the table
             $saved_data = $this->clientModel->find($save_id);
             
            return json_encode(array("success" => true, 'data' => $this->_make_row($saved_data), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            return json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
             if ($this->clientModel->update($id, ['deleted_at' => null])) {
                return json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            }
        } else {
             if ($id && $this->clientModel->delete($id)) {
                return json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            }
        }
        return json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
    }
    
    // Helper to format row data for the table (used in save/update)
    private function _make_row($data) {
        // This should match the columns in your JS table definition
        return [
            $data['id'],
            anchor(get_uri("clients/view/" . $data['id']), $data['company_name']),
            $data['primary_contact'] ?? '-', // Placeholder
            $data['total_projects'] ?? '0',
            $data['invoice_value'] ?? '0.00',
            $data['payment_received'] ?? '0.00',
            $data['due'] ?? '0.00',
            // Actions
            modal_anchor(get_uri("clients/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_client'), "data-post-id" => $data['id']))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_client'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("clients/delete"), "data-action" => "delete"))
        ];
    }
    
    // Helper to get row data by ID
    private function _row_data($id) {
         $data = $this->clientModel->getDetails($id); // Using getDetails to get calculated fields
         return $this->_make_row($data); 
    }

    // FILE MANAGEMENT METHODS
    
    public function file_modal_form() {
        $client_id = $this->request->getPost('client_id') ?? $this->request->getPost('id');
        return view('clients/files/modal_form', ['client_id' => $client_id]);
    }

    public function save_file() {
        $client_id = $this->request->getPost('client_id');
        
        $validationRule = [
            'files' => [
                'label' => 'File',
                'rules' => 'uploaded[files]'
            ],
        ];

        if (!$this->validate($validationRule)) {
             return json_encode(array("success" => false, 'message' => $this->validator->getErrors()));
        }

        $files = $this->request->getFiles();
        
        if ($files) {
             foreach ($files['files'] as $file) {
                if ($file->isValid() && ! $file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $uploadPath = WRITEPATH . 'uploads/clients/' . $client_id;
                    
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    $file->move($uploadPath, $newName);
                    
                    $fileModel = new GeneralFileModel();
                    $fileModel->save([
                        'client_id' => $client_id,
                        'file_name' => $file->getClientName(),
                        'file_size' => $file->getSize() / 1024 / 1024,
                        'sys_file_name' => $newName,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
             }
             return json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        }
        
         return json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
    }

    public function delete_file() {
        $id = $this->request->getPost('id');
        $fileModel = new GeneralFileModel();
        
        if ($id && $fileModel->delete($id)) {
             return json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        }
        return json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
    }
    
    public function download_file($id) {
        $fileModel = new GeneralFileModel();
        $file = $fileModel->find($id);
        
        if ($file) {
             $uploadPath = WRITEPATH . 'uploads/clients/' . $file['client_id'] . '/';
             $filename = $file['sys_file_name'] ?? $file['file_name'];
             $filePath = $uploadPath . $filename;
             
             if (file_exists($filePath)) {
                 return $this->response->download($filePath, null)->setFileName($file['file_name']);
             }
        }
        return "File not found.";
    }

    // CONTACT MANAGEMENT

    public function contact_profile($contact_id, $tab = "general") {
        $userModel = new \App\Models\FsUserModel(); 
        $contact = $userModel->find($contact_id);

        if (!$contact) {
            // show_404(); 
            return redirect()->back()->with('error', 'Contact not found');
        }

        // Basic view data
        $view_data['user_info'] = (object)$contact;
        $view_data['client_info'] = (object)$this->clientModel->find($contact['client_id']);
        $view_data['tab'] = $tab;
        
        return view('clients/contacts/view', $view_data);
    }
    
    public function save_contact() {
        $contact_id = $this->request->getPost('contact_id');
        $client_id = $this->request->getPost('client_id');

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|valid_email'
        ];
        
        if (!$this->validate($rules)) {
             return json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
        
        $userModel = new \App\Models\FsUserModel();
        
        // Check email uniqueness logic (simplified)
        $existing = $userModel->where('email', $this->request->getPost('email'))->first();
        if ($existing && $existing['id'] != $contact_id) {
             return json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
        }

        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'job_title' => $this->request->getPost('job_title'),
            'client_id' => $client_id,
            'user_type' => 'client' 
        ];
        
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($userModel->save(array_merge($userData, $contact_id ? ['id' => $contact_id] : []))) {
            return json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        }
        
        return json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
    }

}
