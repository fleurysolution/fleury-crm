<?php

namespace App\Controllers;

use App\Models\AutomationRuleModel;
use App\Models\AutomationLogModel;

class Automations extends BaseAppController
{
    protected $ruleModel;
    protected $logModel;

    public function __construct()
    {
        $this->ruleModel = new AutomationRuleModel();
        $this->logModel  = new AutomationLogModel();

        if (!session()->get('is_admin') && !in_array('admin', session()->get('user_roles') ?? [])) {
            header('Location: ' . site_url('dashboard'));
            exit;
        }
    }

    public function index()
    {
        $tenantId = session()->get('tenant_id');
        $data = [
            'title' => 'Automations Control Center',
            'rules' => $this->ruleModel->where('tenant_id', $tenantId)->findAll(),
            'logs'  => $this->logModel->where('tenant_id', $tenantId)->orderBy('executed_at', 'DESC')->limit(10)->find()
        ];
        return $this->render('settings/automations/index', $data);
    }

    public function store()
    {
        $tenantId = session()->get('tenant_id');
        
        $conditions = [];
        if ($this->request->getPost('cond_field')) {
            $conditions[] = [
                'field'    => $this->request->getPost('cond_field'),
                'operator' => $this->request->getPost('cond_op'),
                'value'    => $this->request->getPost('cond_val')
            ];
        }

        $actionConfig = [
            'field' => $this->request->getPost('action_field'),
            'value' => $this->request->getPost('action_val'),
            'title' => $this->request->getPost('task_title'),
            'description' => $this->request->getPost('task_desc')
        ];

        $this->ruleModel->insert([
            'tenant_id'      => $tenantId,
            'name'           => $this->request->getPost('name'),
            'trigger_type'   => $this->request->getPost('trigger_type'),
            'trigger_object' => $this->request->getPost('trigger_object'),
            'conditions'     => json_encode($conditions),
            'action_type'    => $this->request->getPost('action_type'),
            'action_config'  => json_encode($actionConfig),
            'is_active'      => 1
        ]);

        return redirect()->to('settings/automations')->with('message', 'Automation rule created.');
    }

    public function toggle(int $id)
    {
        $rule = $this->ruleModel->find($id);
        if ($rule) {
            $this->ruleModel->update($id, ['is_active' => !$rule['is_active']]);
        }
        return redirect()->to('settings/automations');
    }
}
