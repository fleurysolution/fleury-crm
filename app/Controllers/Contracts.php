<?php

namespace App\Controllers;

use App\Models\ContractModel;
use App\Models\ContractAmendmentModel;
use App\Models\ProjectModel;

class Contracts extends BaseAppController
{
    /**
     * GET /projects/:id/contracts — list all contracts for a project
     */
    public function index(int $projectId): string
    {
        $project   = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $cModel    = new ContractModel();
        $contracts = $cModel->forProject($projectId);
        $totalVal  = $cModel->totalValue($projectId);

        return $this->render('contracts/index', [
            'project'   => $project,
            'contracts' => $contracts,
            'totalVal'  => $totalVal,
        ]);
    }

    /**
     * GET /contracts/:id — contract detail with amendments
     */
    public function show(int $id): string
    {
        $cModel   = new ContractModel();
        $contract = $cModel->find($id);
        if (!$contract) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $project    = (new ProjectModel())->find($contract['project_id']);
        $aModel     = new ContractAmendmentModel();
        $amendments = $aModel->forContract($id);
        $totalChg   = $aModel->totalApprovedChange($id);
        $currentVal = $contract['value'] + $totalChg;

        return $this->render('contracts/show', [
            'project'    => $project,
            'contract'   => $contract,
            'amendments' => $amendments,
            'totalChg'   => $totalChg,
            'currentVal' => $currentVal,
        ]);
    }

    /**
     * POST /projects/:id/contracts — create contract
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $cModel = new ContractModel();
        $data   = [
            'project_id'      => $projectId,
            'contract_number' => $cModel->nextNumber($projectId),
            'title'           => $this->request->getPost('title'),
            'type'            => $this->request->getPost('type') ?: 'main',
            'contractor_name' => $this->request->getPost('contractor_name'),
            'scope'           => $this->request->getPost('scope'),
            'value'           => (float)$this->request->getPost('value'),
            'currency'        => $this->request->getPost('currency') ?: 'USD',
            'retention_pct'   => (float)($this->request->getPost('retention_pct') ?: 10),
            'start_date'      => $this->request->getPost('start_date') ?: null,
            'end_date'        => $this->request->getPost('end_date')   ?: null,
            'status'          => 'draft',
            'created_by'      => $this->currentUser['id'],
        ];
        $id = $cModel->insert($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'id' => $id, 'number' => $data['contract_number']]);
        }
        return redirect()->to(site_url("contracts/{$id}"))->with('success', 'Contract ' . $data['contract_number'] . ' created.');
    }

    /**
     * POST /contracts/:id/status — quick status change
     */
    public function updateStatus(int $id): \CodeIgniter\HTTP\Response
    {
        $allowed = ['draft','active','on_hold','completed','terminated'];
        $status  = $this->request->getPost('status');
        if (!in_array($status, $allowed)) return $this->response->setJSON(['success' => false]);
        (new ContractModel())->update($id, ['status' => $status]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /contracts/:id/amend — add a variation order
     */
    public function amend(int $id): \CodeIgniter\HTTP\Response
    {
        $aModel   = new ContractAmendmentModel();
        $last     = count($aModel->forContract($id));
        $amendId  = $aModel->insert([
            'contract_id'  => $id,
            'amendment_no' => $last + 1,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'value_change' => (float)$this->request->getPost('value_change'),
            'time_change'  => (int)$this->request->getPost('time_change'),
            'status'       => 'pending',
        ]);
        $amend = $aModel->find($amendId);
        return $this->response->setJSON(['success' => true, 'amendment' => $amend]);
    }

    /**
     * POST /contracts/amendments/:id/approve
     */
    public function approveAmendment(int $amendId): \CodeIgniter\HTTP\Response
    {
        (new ContractAmendmentModel())->update($amendId, [
            'status'      => 'approved',
            'approved_by' => $this->currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /contracts/:id/delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $cModel  = new ContractModel();
        $cnt     = $cModel->find($id);
        $cModel->delete($id);
        return redirect()->to(site_url("projects/{$cnt['project_id']}?tab=contracts"))
            ->with('success', 'Contract deleted.');
    }
}
