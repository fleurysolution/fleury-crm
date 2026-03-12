<?php

namespace App\Controllers;

use App\Models\ContractModel;
use App\Models\ContractAmendmentModel;
use App\Models\ProjectModel;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $updateData = [
            'status'      => 'approved',
            'approved_by' => $this->currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ];

        $sigData = $this->request->getPost('signature_data');
        if ($sigData) {
            $updateData['signature_data'] = $sigData;
            $updateData['signature_ip']   = $this->request->getIPAddress();
            $updateData['signed_at']      = date('Y-m-d H:i:s');
        }

        $aModel = new ContractAmendmentModel();
        $aModel->update($amendId, $updateData);

        $amend = $aModel->find($amendId);
        if ($amend) {
            $contract = (new ContractModel())->find($amend['contract_id']);
            if ($contract && $this->currentUser['id'] !== $contract['created_by']) {
                \App\Models\NotificationModel::send(
                    $contract['created_by'],
                    'vo_approved',
                    "Variation Order #{$amend['amendment_no']} Approved",
                    ['url' => "projects/{$contract['project_id']}?tab=contracts"]
                );
            }
        }
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /contracts/amendments/:id/reject
     */
    public function rejectAmendment(int $amendId): \CodeIgniter\HTTP\Response
    {
        $reason = $this->request->getPost('reason');
        $aModel = new ContractAmendmentModel();
        
        $aModel->update($amendId, [
            'status' => 'rejected',
            // if there's a notes field in db we could put notes here, but let's assume no dedicated field in amendment model
        ]);

        $amend = $aModel->find($amendId);
        if ($amend) {
            $contract = (new ContractModel())->find($amend['contract_id']);
            if ($contract && current_user_id() !== $contract['created_by']) {
                \App\Models\NotificationModel::send(
                    $contract['created_by'],
                    'vo_rejected',
                    "Variation Order #{$amend['amendment_no']} Rejected",
                    [
                        'body' => $reason ? "Reason: $reason" : 'No reason provided.',
                        'url'  => "projects/{$contract['project_id']}?tab=contracts"
                    ]
                );
            }
        }
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /contracts/:id/sign
     * Receives a base64 encoded signature from the frontend canvas.
     */
    public function signContract(int $id): \CodeIgniter\HTTP\Response
    {
        $cModel = new ContractModel();
        $contract = $cModel->find($id);

        if (!$contract) {
            return $this->response->setJSON(['success' => false, 'message' => 'Contract not found.']);
        }

        $signatureData = $this->request->getPost('signature_data');
        if (empty($signatureData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No signature data provided.']);
        }

        // Determine if the current user is signing as the Client or the Subcontractor (or Project Manager/Admin as the internal builder)
        // For simplicity, we'll check if the user is a client. If not, we assume they are signing as the internal contractor.
        $roleSlug = session()->get('role_slug') ?? 'employee';
        
        $updateData = [];
        
        if ($roleSlug === 'client') {
            $updateData = [
                'client_signed_at'      => date('Y-m-d H:i:s'),
                'client_ip_address'     => $this->request->getIPAddress(),
                'client_signature_data' => $signatureData,
            ];
        } else {
            // Assume Contractor / Builder
            $updateData = [
                'contractor_signed_at'      => date('Y-m-d H:i:s'),
                'contractor_ip_address'     => $this->request->getIPAddress(),
                'contractor_signature_data' => $signatureData,
            ];
        }

        // Check if both parties have signed to mark the contract as active
        $isClientSigned = !empty($contract['client_signature_data']) || isset($updateData['client_signature_data']);
        $isContractorSigned = !empty($contract['contractor_signature_data']) || isset($updateData['contractor_signature_data']);
        
        if ($isClientSigned && $isContractorSigned) {
            $updateData['status'] = 'active';
            $updateData['signed_at'] = date('Y-m-d H:i:s');
            $updateData['signed_by'] = $this->currentUser['id'];
        }

        $cModel->update($id, $updateData);

        return $this->response->setJSON(['success' => true, 'message' => 'Contract signed successfully.']);
    }

    /**
     * GET /contracts/:id/pdf
     * Generates a PDF version of the contract and its signatures
     */
    public function downloadPdf(int $id)
    {
        $cModel = new ContractModel();
        $contract = $cModel->find($id);

        if (!$contract) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $project = (new ProjectModel())->find($contract['project_id']);
        $aModel = new ContractAmendmentModel();
        $amendments = $aModel->forContract($id);
        $totalChg = $aModel->totalApprovedChange($id);
        $currentVal = $contract['value'] + $totalChg;

        // Ensure Dompdf is available
        if (!class_exists('\Dompdf\Dompdf')) {
            return redirect()->back()->with('error', 'PDF Generator library not installed.');
        }

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new \Dompdf\Dompdf($options);

        // Render PDF HTML from a view (we will create this view next)
        $html = view('contracts/pdf_template', [
            'project'    => $project,
            'contract'   => $contract,
            'amendments' => $amendments,
            'totalChg'   => $totalChg,
            'currentVal' => $currentVal,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Contract_' . $contract['contract_number'] . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
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
