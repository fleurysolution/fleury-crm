<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response.If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('is_logged_in')) {
            return redirect()->to(site_url('auth/signin'))->with('errors', ['Please sign in to access this page.']);
        }

        // Redirect vendor users trying to access main CRM routes
        $roles = session()->get('user_roles') ?? [];
        if (in_array('subcontractor_vendor', $roles) && count($roles) === 1) {
            // Allow them to hit the signout route. Allow vendor portal routes (which shouldn't use this filter ideally, but just in case)
            $uri = $request->getUri()->getPath();
            if (strpos($uri, 'vendor-portal') === false && strpos($uri, 'auth/signout') === false) {
                return redirect()->to(site_url('vendor-portal/dashboard'));
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
