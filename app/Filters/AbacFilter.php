<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AbacFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // If not logged in, let AuthFilter handle it
        if (!$session->get('is_logged_in')) {
            return;
        }

        // For Super Admins, we might bypass ABAC (assuming role_id 1 or 'admin' slug)
        // Adjust condition based on actual admin slug.
        $roles = $session->get('user_roles') ?? [];
        if (in_array('admin', $roles) || in_array('superadmin', $roles)) {
            return;
        }

        // Enforce ABAC rules:
        // E.g., if a user has 'branch' geo_access_permission, they MUST have a branch_id assigned.
        $geoAccess = $session->get('geo_access_permission');
        $branchId  = $session->get('branch_id');

        if ($geoAccess === 'branch' && empty($branchId)) {
            // User is designated for Branch-level access but has no branch assigned.
            return redirect()->to('/')->with('error', 'Access Denied: You have branch-level access but no branch is assigned to your account.');
        }

        // We can expose the ABAC scope globally for Models to use
        // This is useful for ErpModel to automatically append WHERE clauses
        config('App')->abacScope = [
            'geo_access' => $geoAccess,
            'branch_id'  => $branchId,
            'tenant_id'  => $session->get('tenant_id')
        ];
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
