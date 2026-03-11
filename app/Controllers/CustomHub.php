<?php

namespace App\Controllers;

use App\Models\CustomObjectModel;

class CustomHub extends BaseAppController
{
    protected $objectModel;

    public function __construct()
    {
        $this->objectModel = new CustomObjectModel();
        
        // Ensure only admins can access custom hub
        if (!session()->get('is_admin') && !in_array('admin', session()->get('user_roles') ?? [])) {
            header('Location: ' . site_url('dashboard'));
            exit;
        }
    }

    public function index()
    {
        $tenantId = $this->loginUser->tenant_id;
        
        $data = [
            'title' => 'Custom Hub · BPMS247',
            'objects' => $this->objectModel->forTenant($tenantId)
        ];

        return $this->render('settings/custom_hub/index', $data);
    }

    public function store()
    {
        $tenantId = $this->loginUser->tenant_id;
        
        $name = strtolower(str_replace(' ', '_', $this->request->getPost('label_plural')));

        $data = [
            'tenant_id'      => $tenantId,
            'name'           => $name,
            'label_singular' => $this->request->getPost('label_singular'),
            'label_plural'   => $this->request->getPost('label_plural'),
            'description'    => $this->request->getPost('description'),
            'status'         => 'active'
        ];

        $this->objectModel->insert($data);
        return redirect()->to('settings/custom-hub')->with('message', 'Custom object created successfully.');
    }

    public function delete(int $id)
    {
        $tenantId = $this->loginUser->tenant_id;
        $object = $this->objectModel->where('tenant_id', $tenantId)->find($id);
        
        if ($object) {
            $this->objectModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Object deleted.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Object not found.']);
    }

    public function viewData(string $objectName)
    {
        $tenantId = $this->loginUser->tenant_id;
        $object = $this->objectModel->where('name', $objectName)
                                    ->where('tenant_id', $tenantId)
                                    ->first();
        
        if (!$object) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Custom object not found: " . $objectName);
        }

        $fieldModel = new \App\Models\CustomFieldModel();
        $fields = $fieldModel->where('object_type', $objectName)->where('tenant_id', $tenantId)->findAll();

        $recordModel = new \App\Models\CustomObjectRecordModel();
        $records = $recordModel->where('custom_object_id', $object['id'])->where('tenant_id', $tenantId)->findAll();

        $valueModel = new \App\Models\CustomFieldValueModel();
        $lookupOptions = [];

        foreach ($fields as $f) {
            if ($f['field_type'] === 'lookup') {
                $target = $f['options']; // stored target object name
                $lookupOptions[$f['id']] = $this->getLookupOptions($target, $tenantId);
            }
        }

        foreach ($records as &$record) {
            $values = $valueModel->where('record_id', $record['id'])->findAll();
            foreach ($values as $val) {
                $fieldId = $val['field_id'];
                $displayVal = $val['value'];
                
                // If it's a lookup, find the label
                if (isset($lookupOptions[$fieldId])) {
                   foreach ($lookupOptions[$fieldId] as $opt) {
                       if ($opt['id'] == $val['value']) {
                           $displayVal = $opt['label'];
                           break;
                       }
                   }
                }
                
                $record['field_' . $fieldId] = $displayVal;
                $record['raw_field_' . $fieldId] = $val['value'];
            }
        }

        $data = [
            'title' => $object['label_plural'] . ' · BPMS247',
            'object' => $object,
            'fields' => $fields,
            'records' => $records,
            'lookupOptions' => $lookupOptions
        ];

        return $this->render('hub/data_view', $data);
    }

    public function saveData(string $objectName)
    {
        $tenantId = $this->loginUser->tenant_id;
        $object = $this->objectModel->where('name', $objectName)
                                    ->where('tenant_id', $tenantId)
                                    ->first();
        
        if (!$object) return redirect()->back()->with('error', 'Object not found.');

        $recordModel = new \App\Models\CustomObjectRecordModel();
        $recordId = $recordModel->insert([
            'tenant_id' => $tenantId,
            'custom_object_id' => $object['id']
        ]);

        $valueModel = new \App\Models\CustomFieldValueModel();
        $fieldModel = new \App\Models\CustomFieldModel();
        $fields = $fieldModel->where('object_type', $objectName)->where('tenant_id', $tenantId)->findAll();

        foreach ($fields as $field) {
            $val = $this->request->getPost('field_' . $field['id']);
            $valueModel->insert([
                'record_id' => $recordId,
                'field_id'  => $field['id'],
                'value'     => $val
            ]);
        }

        return redirect()->back()->with('message', $object['label_singular'] . ' saved successfully.');
    }
    protected function getLookupOptions(string $target, int $tenantId): array
    {
        $db = \Config\Database::connect();
        $results = [];

        switch ($target) {
            case 'projects':
                return $db->table('projects')->select('id, title as label')->where('tenant_id', $tenantId)->get()->getResultArray();
            case 'clients':
                return $db->table('clients')->select('id, company_name as label')->where('tenant_id', $tenantId)->get()->getResultArray();
            case 'leads':
                $rows = $db->table('leads')->select('id, first_name, last_name')->where('tenant_id', $tenantId)->get()->getResultArray();
                foreach($rows as $r) { $results[] = ['id' => $r['id'], 'label' => $r['first_name'] . ' ' . $r['last_name']]; }
                return $results;
            default:
                $targetObj = $this->objectModel->where('name', $target)->where('tenant_id', $tenantId)->first();
                if ($targetObj) {
                    $recordModel = new \App\Models\CustomObjectRecordModel();
                    $recs = $recordModel->where('custom_object_id', $targetObj['id'])->where('tenant_id', $tenantId)->findAll();
                    $valueModel = new \App\Models\CustomFieldValueModel();
                    foreach ($recs as $r) {
                        $firstVal = $valueModel->where('record_id', $r['id'])->first();
                        $results[] = [
                            'id' => $r['id'],
                            'label' => $firstVal ? $firstVal['value'] : ('Record #' . $r['id'])
                        ];
                    }
                }
                return $results;
        }
    }
}
