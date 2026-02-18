<?php

namespace App\Controllers;

use App\Services\AuthService;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = service('authService');
        helper(['url', 'form']);
    }

    public function signin()
    {
        return view('auth/signin', [
                'title'  => 'Sign In',
                'errors' => session()->getFlashdata('errors') ?? [],
            ]);
    }

    public function attemptSignin(): RedirectResponse
    {
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $result = $this->authService->attemptSignin($email, $password, $this->request->getIPAddress(), (string) $this->request->getUserAgent());

        if (!$result['success']) {
            return redirect()->to(site_url('auth/signin'))
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('/'));
    }

    public function signout(): RedirectResponse
    {
        $this->authService->signout();
        return redirect()->to(site_url('auth/signin'))->with('message', 'Signed out successfully.');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password', [
            'title'  => 'Forgot Password',
            'logoUrl' => base_url('assets/img/logo.png'),
            'errors' => session()->getFlashdata('errors') ?? [],
            'status' => session()->getFlashdata('status') ?? null,
        ]);
    }

    public function sendResetLink(): RedirectResponse
    {
        $email = trim((string) $this->request->getPost('email'));

        $result = $this->authService->sendPasswordResetLink($email, $this->request->getIPAddress(), (string) $this->request->getUserAgent());

        if (!$result['success']) {
            session()->set('user_locale', $user['locale'] ?? 'en');
            return redirect()->to(site_url('auth/password/forgot'))->withInput()->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('auth/password/forgot'))->with('status', $result['message']);
    }

    public function resetPassword(string $token)
    {
        return view('auth/reset_password', [
            'title'  => 'Reset Password',
            'token'  => $token,
            'errors' => session()->getFlashdata('errors') ?? [],
            'status' => session()->getFlashdata('status') ?? null,
        ]);
    }

    public function doResetPassword(): RedirectResponse
    {
        $token    = (string) $this->request->getPost('token');
        $password = (string) $this->request->getPost('password');

        $result = $this->authService->resetPassword($token, $password);

        if (!$result['success']) {
            return redirect()->to(site_url('auth/password/reset/' . urlencode($token)))
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('auth/signin'))->with('status', $result['message']);
    }
}
