<?php

namespace App\Controllers;

use App\Models\BidPackageModel;
use App\Models\BidModel;
use App\Models\ProjectModel;

class Bidding extends BaseAppController
{
    protected $packages;
    protected $bids;
    protected $projects;

    public function __construct()
    {
        $this->packages = new BidPackageModel();
        $this->bids     = new BidModel();
        $this->projects = new ProjectModel();
    }

    public function storePackage(int $projectId)
    {
        $project   = $this->projects->find($projectId);
        $tenantId  = session()->get('tenant_id') ?: ($project['tenant_id'] ?? null);
        
        $data = [
            'tenant_id'   => $tenantId,
            'project_id'  => $projectId,
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'due_date'    => $this->request->getPost('due_date'),
            'status'      => 'open'
        ];

        $this->packages->insert($data);
        return redirect()->to(site_url("projects/{$projectId}?tab=bidding"))->with('message', 'Bid package published.');
    }

    public function submitBid(int $packageId)
    {
        $tenantId = session()->get('tenant_id');
        $package = $this->packages->find($packageId);
        
        if (!$package) return $this->response->setJSON(['success' => false, 'message' => 'Package not found.']);

        $data = [
            'tenant_id'   => $tenantId,
            'package_id'  => $packageId,
            'vendor_name' => $this->request->getPost('vendor_name'),
            'amount'      => $this->request->getPost('amount'),
            'notes'       => $this->request->getPost('notes'),
            'status'      => 'submitted'
        ];

        $this->bids->insert($data);
        return redirect()->to(site_url("projects/{$package['project_id']}?tab=bidding"))->with('message', 'Bid submitted successfully.');
    }

    public function award(int $bidId)
    {
        $tenantId = session()->get('tenant_id');
        $bid = $this->bids->find($bidId);
        
        if ($bid) {
            // Close package
            $this->packages->update($bid['package_id'], ['status' => 'awarded']);
            // Award bid
            $this->bids->update($bidId, ['status' => 'awarded']);
            // Reject others
            $this->bids->where('package_id', $bid['package_id'])->where('id !=', $bidId)->update(null, ['status' => 'rejected']);
        }

        return redirect()->back()->with('message', 'Bid awarded.');
    }
}
