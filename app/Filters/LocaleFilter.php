<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LocaleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $appConfig = config('App');
        $supported = $appConfig->supportedLocales ?? ['en'];

        // 1) Logged-in user preference (session)
        $userLocale = session('user_locale');
        if ($userLocale && in_array($userLocale, $supported, true)) {
            service('request')->setLocale($userLocale);
            return;
        }

        // 2) Cookie locale
        $cookieLocale = $request->getCookie('fs_locale');
        if ($cookieLocale && in_array($cookieLocale, $supported, true)) {
            service('request')->setLocale($cookieLocale);
            return;
        }

        // 3) Session locale (optional)
        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, $supported, true)) {
            service('request')->setLocale($sessionLocale);
            return;
        }

        // 4) CI4 negotiated locale (Accept-Language) if enabled, else default
        // If negotiateLocale=true, CI already determines locale, but we keep a safe fallback:
        $fallback = $appConfig->defaultLocale ?? 'en';
        service('request')->setLocale($request->getLocale() ?: $fallback);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
