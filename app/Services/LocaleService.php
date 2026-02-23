<?php

namespace App\Services;

use CodeIgniter\HTTP\RequestInterface;

class LocaleService
{
    /**
     * Detect and apply the correct locale to the current request.
     * Priority: session > settings > app default.
     */
    public function applyLocale(RequestInterface $request): void
    {
        $locale = session()->get('locale');

        if (! $locale) {
            // Fall back to the setting stored in the DB / cache
            $locale = service('settingsService')->get('default_language', config('App')->defaultLocale ?? 'en');
        }

        // Normalise: e.g. "english" -> "en", "french" -> "fr"
        $locale = $this->normalise($locale);

        // Apply to incoming request (CodeIgniter respects this for translations)
        if (method_exists($request, 'setLocale')) {
            $request->setLocale($locale);
        }
    }

    /**
     * Store a locale choice in the session.
     */
    public function setLocale(string $locale): void
    {
        session()->set('locale', $this->normalise($locale));
    }

    // -----------------------------------------------------------------------

    private function normalise(string $locale): string
    {
        $map = [
            'english' => 'en',
            'spanish' => 'es',
            'french'  => 'fr',
            'german'  => 'de',
            'arabic'  => 'ar',
        ];

        $lower = strtolower(trim($locale));

        return $map[$lower] ?? $lower;
    }
}
