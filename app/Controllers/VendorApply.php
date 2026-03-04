<?php

namespace App\Controllers;

use App\Models\VendorApplicationModel;

class VendorApply extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        return view('vendor_portal/apply', [
            'title' => 'Vendor Registration',
            'errors' => session()->getFlashdata('errors') ?? []
        ]);
    }

    public function submit()
    {
        // Validation
        $rules = [
            'company_name' => 'required|min_length[3]|max_length[255]',
            'contact_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email',
            'phone' => 'permit_empty|min_length[5]|max_length[50]',
            'trade_type' => 'permit_empty|max_length[100]',
            'tax_id' => 'permit_empty|max_length[100]',
            'w9_file' => 'permit_empty|uploaded[w9_file]|max_size[w9_file,5120]|ext_in[w9_file,pdf,jpg,jpeg,png]',
            'insurance_file' => 'permit_empty|uploaded[insurance_file]|max_size[insurance_file,5120]|ext_in[insurance_file,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $w9File = $this->request->getFile('w9_file');
        $w9Path = null;
        if ($w9File && $w9File->isValid() && !$w9File->hasMoved()) {
            $newName = $w9File->getRandomName();
            $w9File->move(FCPATH . 'uploads/vendors/w9', $newName);
            $w9Path = 'uploads/vendors/w9/' . $newName;
        }

        $insuranceFile = $this->request->getFile('insurance_file');
        $insurancePath = null;
        if ($insuranceFile && $insuranceFile->isValid() && !$insuranceFile->hasMoved()) {
            $newName = $insuranceFile->getRandomName();
            $insuranceFile->move(FCPATH . 'uploads/vendors/insurance', $newName);
            $insurancePath = 'uploads/vendors/insurance/' . $newName;
        }

        $model = new VendorApplicationModel();
        
        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'contact_name' => $this->request->getPost('contact_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'trade_type' => $this->request->getPost('trade_type'),
            'tax_id' => $this->request->getPost('tax_id'),
            'w9_path' => $w9Path,
            'insurance_path' => $insurancePath,
            'status' => 'pending'
        ];

        $model->insert($data);

        return redirect()->to(site_url('vendor/apply/success'))->with('message', 'Application submitted successfully. We will contact you soon.');
    }

    public function success()
    {
        return view('vendor_portal/success', [
            'title' => 'Application Submitted',
            'message' => session()->getFlashdata('message') ?? 'Your application has been received.'
        ]);
    }
}
