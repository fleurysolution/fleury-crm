<?php

namespace App\Controllers;

use App\Models\SubscriptionPackageModel;
use App\Services\RegistrationService;
use App\Services\StripeService;

class Signup extends BaseController
{
    protected $packageModel;
    protected $registrationService;
    protected $stripeService;

    public function __construct()
    {
        $this->packageModel = new SubscriptionPackageModel();
        $this->registrationService = new RegistrationService();
        $this->stripeService = new StripeService();
        helper(['form', 'url']);
    }

    /**
     * Choose Plan
     */
    public function index()
    {
        $data = [
            'title'    => 'Choose Your Plan · BPMS247',
            'packages' => $this->packageModel->getActivePackages()
        ];
        return view('signup/index', $data);
    }

    /**
     * Account Details (Step 1)
     */
    public function account($packageId)
    {
        $package = $this->packageModel->find($packageId);
        if (!$package) {
            return redirect()->to('/signup')->with('error', 'Invalid plan selected.');
        }

        session()->set('signup_package_id', $packageId);

        $data = [
            'title'   => 'Create Your Account · BPMS247',
            'step'    => 1,
            'package' => $package
        ];
        return view('signup/form_wizard', $data);
    }

    /**
     * Company Details (Step 2)
     */
    public function company()
    {
        if ($this->request->getMethod() === 'POST') {
            // Validate Step 1
            $rules = [
                'first_name' => 'required|min_length[2]',
                'last_name'  => 'required|min_length[2]',
                'email'      => 'required|valid_email|is_unique[fs_users.email]',
                'password'   => 'required|min_length[8]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            session()->set('signup_user', $this->request->getPost());
        }

        if (!session()->get('signup_user')) {
             return redirect()->to('/signup');
        }

        $data = [
            'title' => 'Company Details · BPMS247',
            'step'  => 2
        ];
        return view('signup/form_wizard', $data);
    }

    /**
     * Finalize Registration
     */
    public function submit()
    {
        $rules = [
            'company_name'   => 'required|min_length[3]',
            'industry'       => 'required',
            'employee_count' => 'required|numeric',
            'country'        => 'required',
            'currency'       => 'required',
            'timezone'       => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $companyData = [
            'name'           => $this->request->getPost('company_name'),
            'industry'       => $this->request->getPost('industry'),
            'employee_count' => $this->request->getPost('employee_count'),
            'country'        => $this->request->getPost('country'),
            'currency'       => $this->request->getPost('currency'),
            'timezone'       => $this->request->getPost('timezone'),
        ];

        session()->set('signup_company', $companyData);

        $userData = session()->get('signup_user');
        $packageId = session()->get('signup_package_id');
        $package = $this->packageModel->find($packageId);

        try {
            // Instead of immediate registration, redirect to Stripe
            $checkoutSession = $this->stripeService->createCheckoutSession($companyData, $package, $userData['email']);
            return redirect()->to($checkoutSession->url);
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }

    /**
     * Success return from Stripe
     */
    public function success()
    {
        $sessionId = $this->request->getGet('session_id');
        if (!$sessionId) return redirect()->to('/signup');

        $companyData = session()->get('signup_company');
        $userData = session()->get('signup_user');
        $packageId = session()->get('signup_package_id');

        if (!$companyData || !$userData || !$packageId) {
            return redirect()->to('/signup')->with('error', 'Session expired during payment process.');
        }

        try {
            // TODO: In a production app, verify the Stripe session status before provisioning
            $tenantId = $this->registrationService->registerTenant($companyData, $userData, $packageId);
            
            // Clear onboarding session
            session()->remove(['signup_user', 'signup_company', 'signup_package_id']);

            return redirect()->to('auth/signin')->with('message', 'Registration and payment successful! Welcome to BPMS247.');
            
        } catch (\Exception $e) {
            return redirect()->to('/signup')->with('error', 'Success redirected but provisioning failed: ' . $e->getMessage());
        }
    }
}
