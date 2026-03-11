<?php

namespace App\Controllers;

use App\Models\SubscriptionPackageModel;

class Subscriptions extends BaseAppController
{
    protected $packageModel;

    public function __construct()
    {
        $this->packageModel = new SubscriptionPackageModel();
    }

    public function index()
    {
        // Only Super Admins (tenant_id = 1 or null) can access this
        if ($this->loginUser->tenant_id !== null && $this->loginUser->tenant_id != 1) {
            return redirect()->to('dashboard')->with('error', 'Unauthorized access.');
        }

        $data = [
            'title' => 'Subscription Packages · BPMS247',
            'packages' => $this->packageModel->findAll()
        ];

        return $this->render('subscriptions/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Subscription Package · BPMS247'
        ];
        return $this->render('subscriptions/modal_form', $data);
    }

    public function store()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'billing_interval' => $this->request->getPost('billing_interval'),
            'is_per_user' => $this->request->getPost('is_per_user') ? 1 : 0,
            'features' => $this->request->getPost('features'),
            'status' => 'active'
        ];

        $this->packageModel->insert($data);
        return redirect()->to('subscriptions')->with('message', 'Package created successfully.');
    }
    public function current()
    {
        $tenantId = $this->loginUser->tenant_id;
        $subModel = new \App\Models\TenantSubscriptionModel();
        $subscription = $subModel->get_current_subscription($tenantId);
        
        if (!$subscription) {
            return $this->response->setJSON(['success' => false, 'message' => 'No active subscription found.']);
        }

        $package = $this->packageModel->find($subscription['package_id']);
        return $this->response->setJSON(['success' => true, 'subscription' => $subscription, 'package' => $package]);
    }

    public function cancel()
    {
        $tenantId = $this->loginUser->tenant_id;
        $subModel = new \App\Models\TenantSubscriptionModel();
        $subscription = $subModel->get_current_subscription($tenantId);

        if ($subscription) {
            $subModel->update($subscription['id'], ['status' => 'cancelled']);
            (new \App\Models\TenantModel())->update($tenantId, ['status' => 'inactive']);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Subscription cancelled successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to cancel subscription.']);
    }

    public function upgrade()
    {
        $data = [
            'title' => 'Upgrade Your Plan · BPMS247',
            'packages' => $this->packageModel->getActivePackages()
        ];
        return $this->render('subscriptions/upgrade', $data);
    }

    public function renew()
    {
        $data = [
            'title' => 'Renew Your Subscription · BPMS247',
            'packages' => $this->packageModel->getActivePackages()
        ];
        return $this->render('subscriptions/upgrade', $data);
    }

    public function checkout($packageId)
    {
        $package = $this->packageModel->find($packageId);
        if (!$package) {
            return redirect()->back()->with('error', 'Invalid plan selected.');
        }

        $tenantModel = new \App\Models\TenantModel();
        $tenant = $tenantModel->find($this->loginUser->tenant_id);

        $stripe = new \App\Services\StripeService();
        $successUrl = site_url('subscriptions/success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl = site_url('subscription/renew');

        try {
            $session = $stripe->createCheckoutSession((array)$tenant, $package, $this->loginUser->email, $successUrl, $cancelUrl);
            return redirect()->to($session->url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating payment session: ' . $e->getMessage());
        }
    }

    public function success()
    {
        $sessionId = $this->request->getGet('session_id');
        if (!$sessionId) return redirect()->to('dashboard');

        // In production, we'd verify the session with Stripe here.
        // For now, update subscription record.
        $tenantId = $this->loginUser->tenant_id;
        $subModel = new \App\Models\TenantSubscriptionModel();
        
        // Find existing subscription to update or create new one
        $subscription = $subModel->where('tenant_id', $tenantId)->orderBy('id', 'DESC')->first();
        
        $interval = 'monthly'; // Default or from package
        $days = ($interval === 'yearly') ? 365 : 30;
        
        $updateData = [
            'status' => 'active',
            'current_period_start' => date('Y-m-d H:i:s'),
            'current_period_end'   => date('Y-m-d H:i:s', strtotime("+$days days")),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($subscription) {
            $subModel->update($subscription['id'], $updateData);
        } else {
            $updateData['tenant_id'] = $tenantId;
            $updateData['package_id'] = 1; // Fallback
            $subModel->insert($updateData);
        }

        return redirect()->to('dashboard')->with('message', 'Subscription updated successfully! Your account is now active.');
    }
}
