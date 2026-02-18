<?php

namespace App\Controllers;

use App\Models\FsUserModel;

class Locale extends BaseController
{
    public function set(string $locale)
    {
        $supported = config('App')->supportedLocales ?? ['en'];
        if (!in_array($locale, $supported, true)) {
            $locale = config('App')->defaultLocale ?? 'en';
        }

        // store in cookie (1 year)
        set_cookie('fs_locale', $locale, 60 * 60 * 24 * 365);

        // store in session (optional)
        session()->set('locale', $locale);

        // if logged in, store in DB & session user_locale
        $userId = (int) session('user_id');
        if ($userId > 0) {
            (new FsUserModel())->update($userId, ['locale' => $locale]);
            session()->set('user_locale', $locale);
        }

        // redirect back safely
        $back = $this->request->getServer('HTTP_REFERER');
        return redirect()->to($back ?: site_url('/'));
    }
}
