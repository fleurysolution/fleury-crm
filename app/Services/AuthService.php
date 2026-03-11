<?php

namespace App\Services;

use App\Models\FsPasswordResetTokenModel;
use App\Models\FsUserModel;
use App\Services\EmailService;

class AuthService
{
    protected FsUserModel $users;
    protected FsPasswordResetTokenModel $resetTokens;

    public function __construct()
    {
        $this->users       = new FsUserModel();
        $this->resetTokens = new FsPasswordResetTokenModel();
        helper('text');
    }

    public function attemptSignin(string $email, string $password, ?string $ip = null, ?string $userAgent = null): array
    {
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email.';
        }
        if ($password === '') {
            $errors[] = 'Password is required.';
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->users->where('email', $email)->where('deleted_at', null)->first();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            return ['success' => false, 'errors' => ['Invalid email or password.']];
        }

        if (($user['status'] ?? '') !== 'active') {
            return ['success' => false, 'errors' => ['Your account is not active.']];
        }

        session()->regenerate(true);

        // Fetch User Roles & Permissions
        $roles = $this->users->getRoles((int) $user['id']);
        $permissions = $this->users->getPermissions((int) $user['id']);

        // Flatten permissions to simple array of slugs
        $permissionSlugs = array_column($permissions, 'slug');
        $roleSlugs = array_column($roles, 'slug');

        session()->set([
            'user_id'                  => (int) $user['id'],
            'user_email'               => $user['email'],
            'user_name'                => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'user_roles'               => $roleSlugs,
            'user_permissions'         => $permissionSlugs,
            'is_logged_in'             => true,
            
            // Full ABAC context
            'tenant_id'                => $user['tenant_id'] ?? null,
            'branch_id'                => $user['branch_id'] ?? null,
            'department_id'            => $user['department_id'] ?? null,
            'reporting_manager_id'     => $user['reporting_manager_id'] ?? null,
            'approval_authority_level' => $user['approval_authority_level'] ?? 0,
            'geo_access_permission'    => $user['geo_access_permission'] ?? null,
            'payroll_profile_id'       => $user['payroll_profile_id'] ?? null,
            'tax_profile_id'           => $user['tax_profile_id'] ?? null,
            'employment_type'          => $user['employment_type'] ?? null,
        ]);

        $this->users->update((int)$user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return ['success' => true];
    }

    public function signout(): void
    {
        session()->destroy();
    }

    public function sendPasswordResetLink(string $email, ?string $ip = null, ?string $userAgent = null): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'errors' => ['Please enter a valid email address.']];
        }

        $user = $this->users->where('email', $email)->where('deleted_at', null)->first();

        // Security: don't reveal whether user exists
        if (!$user) {
            return ['success' => true, 'message' => 'If the account exists, a reset link has been generated.'];
        }

        $rawToken  = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $tokenHash = hash('sha256', $rawToken);

        $this->resetTokens->insert([
            'user_id'    => (int) $user['id'],
            'token_hash' => $tokenHash,
            'issued_ip'  => $ip,
            'user_agent' => $userAgent,
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            'used_at'    => null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Send password reset email
        $resetUrl = site_url('auth/password/reset/' . $rawToken);
        
        $emailSent = EmailService::sendPasswordReset($email, $resetUrl);
        
        if (!$emailSent) {
            log_message('error', 'Failed to send password reset email to: ' . $email);
            return [
                'success' => true,
                'message' => 'If the account exists, a reset link has been sent to your email.',
            ];
        }

        return [
            'success' => true,
            'message' => 'If the account exists, a reset link has been sent to your email.',
        ];
    }

    public function resetPassword(string $rawToken, string $newPassword): array
    {
        $errors = [];

        if ($rawToken === '') {
            $errors[] = 'Invalid reset token.';
        }

        if (strlen($newPassword) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $tokenHash = hash('sha256', $rawToken);

        $row = $this->resetTokens
            ->where('token_hash', $tokenHash)
            ->where('used_at', null)
            ->first();

        if (!$row) {
            return ['success' => false, 'errors' => ['Reset token is invalid or already used.']];
        }

        if (strtotime((string) $row['expires_at']) < time()) {
            return ['success' => false, 'errors' => ['Reset token has expired.']];
        }

        $userId = (int) $row['user_id'];

        $this->users->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        $this->resetTokens->update((int)$row['id'], [
            'used_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Password reset successfully. Please sign in.'];
    }
}
