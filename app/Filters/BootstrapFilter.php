<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class BootstrapFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load settings (cached) + apply locale
        service('settingsService')->boot();
        service('localeService')->applyLocale($request);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}