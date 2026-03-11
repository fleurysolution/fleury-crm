<?php

namespace App\Controllers;

use App\Models\ProjectBidModel;
use App\Models\ProjectModel;

class Bids extends BaseAppController
{
    /**
     * POST /projects/:id/bids
     * Log a new subcontractor quote/bid.
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $project = (new ProjectModel())->find($projectId);

        $bidData = [
            'tenant_id'     => $project['tenant_id'],
            'branch_id'     => $project['branch_id'],
            'project_id'    => $projectId,
            'trade_package' => $this->request->getPost('trade_package'),
            'vendor_name'   => $this->request->getPost('vendor_name'),
            'bid_amount'    => (float)$this->request->getPost('bid_amount'),
            'status'        => 'Pending',
            'created_by'    => $this->currentUser['id'],
        ];

        // Handle File Upload (Optional Quote PDF)
        $file = $this->request->getFile('quote_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            if (!is_dir(FCPATH . 'uploads/bids')) {
                mkdir(FCPATH . 'uploads/bids', 0777, true);
            }
            $file->move(FCPATH . 'uploads/bids', $newName);
            $bidData['quote_filepath'] = 'bids/' . $newName;
        }

        (new ProjectBidModel())->insert($bidData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to(site_url("projects/{$projectId}?tab=estimates"))->with('success', 'Bid Quote Added.');
    }

    /**
     * POST /bids/:id/status
     * Change a bid's status (Awarded/Rejected)
     */
    public function updateStatus(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $bModel = new ProjectBidModel();
        $bid = $bModel->find($id);
        if (!$bid) return $this->response->setJSON(['success' => false, 'message' => 'Bid not found.']);

        $statusPhase = $this->request->getPost('status'); // Awarded or Rejected
        $remarks     = $this->request->getPost('remarks');

        if (in_array($statusPhase, ['Awarded', 'Rejected', 'Pending'])) {
            $bModel->update($id, [
                'status'  => $statusPhase,
                'remarks' => $remarks
            ]);

            // Dispatch notification to the person who logged the quote
            $actionWord = strtolower($statusPhase); // 'awarded' or 'rejected'
            $notifType  = $statusPhase === 'Awarded' ? 'bid_awarded' : 'bid_rejected';
            $msg        = "Your logged bid for {$bid['vendor_name']} ({$bid['trade_package']}) was {$actionWord}.";
            if (!empty($remarks)) {
                $msg .= " Remarks: " . $remarks;
            }

            \App\Models\NotificationModel::send(
                $bid['created_by'],
                $notifType,
                $msg,
                ['url' => "projects/{$bid['project_id']}?tab=estimates"]
            );
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->back()->with('success', "Bid marked as {$statusPhase}.");
    }

    /**
     * POST /bids/:id/delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $bModel = new ProjectBidModel();
        $bid = $bModel->find($id);
        if ($bid) {
            $bModel->delete($id);
            return redirect()->to(site_url("projects/{$bid['project_id']}?tab=estimates"))
                ->with('success', 'Bid removed.');
        }
        return redirect()->back()->with('error', 'Bid not found.');
    }
}
