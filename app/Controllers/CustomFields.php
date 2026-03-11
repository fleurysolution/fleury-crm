<?php

namespace App\Controllers;

use App\Models\CustomFieldModel;
use App\Models\CustomObjectModel;

class CustomFields extends BaseAppController
{
    protected $fieldModel;
    protected $objectModel;

    public function __construct()
    {
        $this->fieldModel = new CustomFieldModel();
        $this->objectModel = new CustomObjectModel();

        // Ensure only admins can access custom fields
        if (!session()->get('is_admin') && !in_array('admin', session()->get('user_roles') ?? [])) {
            header('Location: ' . site_url('dashboard'));
            exit;
        }
    }

    public function index()
    {
        $tenantId = $this->loginUser->tenant_id;
        
        $data = [
            'title' => 'Custom Fields · BPMS247',
            'fields' => $this->fieldModel->where('tenant_id', $tenantId)->findAll(),
            'custom_objects' => $this->objectModel->forTenant($tenantId)
        ];

        return $this->render('settings/custom_fields/index', $data);
    }

    public function store()
    {
        $tenantId = $this->loginUser->tenant_id;
        
        $field_type = $this->request->getPost('field_type');
        $options    = $this->request->getPost('options');
        
        if ($field_type === 'lookup') {
            $options = $this->request->getPost('lookup_target');
        }

        $data = [
            'tenant_id'   => $tenantId,
            'object_type' => $this->request->getPost('object_type'),
            'field_name'  => $this->request->getPost('field_name'),
            'field_label' => $this->request->getPost('field_label'),
            'field_type'  => $field_type,
            'options'     => $options,
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status'      => 'active'
        ];

        $this->fieldModel->insert($data);
        return redirect()->to('settings/custom-fields')->with('message', 'Custom field created successfully.');
    }

    public function delete(int $id)
    {
        $tenantId = $this->loginUser->tenant_id;
        $field = $this->fieldModel->where('tenant_id', $tenantId)->find($id);
        
        if ($field) {
            $this->fieldModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Field deleted.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Field not found.']);
    }
}
