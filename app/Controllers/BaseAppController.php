<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Libraries\Template;
use App\Models\FsSettingsModel;
use App\Models\FsUserModel;

class BaseAppController extends Controller
{
    protected Template $template;
    protected \CodeIgniter\Session\Session $session;
    protected \CodeIgniter\Validation\Validation $validation;
    protected \CodeIgniter\View\Parser $parser;

    // “global” models only (keep this very small)
    protected FsSettingsModel $settingsModel;
    protected FsUserModel $usersModel;

    // shared state
    protected ?object $loginUser   = null;      // Object version for newer code
    protected array   $currentUser = [];        // Legacy array version (for compatibility)
    protected array   $appSettings = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // services
        $this->session    = service('session');
        $this->validation = service('validation');
        $this->parser     = service('parser');

        // helpers (keep to essentials; move module helpers into those modules)
        helper(['url', 'form', 'cookie', 'i18n', 'number']);

        // template/layout
        $this->template = new Template();

        // models (ONLY the ones needed for global bootstrapping)
        $this->settingsModel = model(FsSettingsModel::class);
        $this->usersModel    = model(FsUserModel::class);

        // bootstrap “app prerequisites”
        $this->bootstrapUserAndSettings();
        $this->bootstrapLocale();
        $this->maybeRedirectLandingPage();
        $this->checkSubscription();
    }

    protected function checkSubscription(): void
    {
        if (!$this->loginUser) return;
        
        // Super Admins (tenant_id = null or specifically marked) bypass this
        // For now, let's assume tenant_id = 1 is the platform owner/super admin container
        if ($this->loginUser->tenant_id === null || $this->loginUser->tenant_id == 1) {
            return;
        }

        $router = service('router');
        $controller = $router->controllerName();
        
        // Exclude certain controllers from blocking
        $excluded = [
            '\App\Controllers\Auth',
            '\App\Controllers\Signup',
            '\App\Controllers\Settings', // Maybe partially exclude?
        ];

        if (in_array($controller, $excluded)) {
            return;
        }

        $subManager = new \App\Services\SubscriptionManager();
        if (!$subManager->isSubscriptionActive($this->loginUser->tenant_id)) {
            $currentPath = parse_url(current_url(), PHP_URL_PATH);
            
            // Allow access to locked and renew pages
            if (strpos($currentPath, 'subscription/locked') === false && strpos($currentPath, 'subscription/renew') === false) {
                header('Location: ' . site_url('subscription/locked'));
                exit;
            }
        }
    }

    protected function bootstrapUserAndSettings(): void
    {
        $loginUserId = $this->usersModel->login_user_id(); // keep your existing method for now

        if ($loginUserId) {
            $this->loginUser   = $this->usersModel->get_one($loginUserId);
            $this->currentUser = (array)$this->loginUser;

            // Ensure session user_name and user['name'] is correct after schema migration
            $fullName = trim(($this->loginUser->first_name ?? '') . ' ' . ($this->loginUser->last_name ?? ''));
            if ($fullName) {
                if (session()->get('user_name') !== $fullName) {
                    session()->set('user_name', $fullName);
                }
                $sessUser = session()->get('user');
                if (is_array($sessUser) && ($sessUser['name'] ?? '') !== $fullName) {
                    $sessUser['name'] = $fullName;
                    session()->set('user', $sessUser);
                }
            }

            // settings can be user-specific
            $settings = $this->settingsModel->get_all_required_settings($loginUserId)->getResult();
        } else {
            // settings without user context (optional)
            $settings = $this->settingsModel->get_all_required_settings(null)->getResult();
        }

        foreach ($settings as $s) {
            $this->appSettings[$s->setting_name] = $s->setting_value;
        }

        // If you still want config('Fs')->app_settings_array
        // keep it, but do it once here (if the config exists):
        if ($fsConfig = config('Fs')) {
            $fsConfig->app_settings_array = $this->appSettings;
        }
    }

    protected function bootstrapLocale(): void
    {
        // Your new priority order should be in one place.
        // For now mirror old behavior: user language else setting('language')
        $language = $this->loginUser?->language ?? ($this->appSettings['language'] ?? 'en');

        // If you moved to fs_users.locale, swap to ->locale and your cookie logic.
        service('request')->setLocale($language);
    }

    protected function maybeRedirectLandingPage(): void
    {
        $landingPage = $this->appSettings['landing_page'] ?? null;

        if ($landingPage && $this->isCurrentUrlSameAsBaseUrl()) {
            // CI4-friendly redirect:
            redirect()->to($landingPage)->send();
            exit;
        }
    }

    protected function isCurrentUrlSameAsBaseUrl(): bool
    {
        $cleanCurrentUrl = str_replace('index.php/', '', current_url());
        return $cleanCurrentUrl === base_url();
    }

    /**
     * Compatibility wrapper for rendering views.
     */
    protected function render(string $view, array $data = []): string
    {
        // Inject global variables that views might expect
        $data['login_user']  = $this->loginUser;
        $data['currentUser'] = $this->currentUser;
        $data['appSettings'] = $this->appSettings;

        return view($view, $data);
    }

    /**
     * CI4-friendly validation wrapper similar to your old validate_submitted_data()
     */
    protected function validateSubmitted(array $rules, bool $returnErrors = false, bool $jsonResponse = true)
    {
        // Add permit_empty automatically when not required
        $final = [];
        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'required') === false) {
                $rule .= '|permit_empty';
            }
            $final[$field] = $rule;
        }

        if (! $final) {
            return true;
        }

        if (! $this->validate($final)) {
            $errors = $this->validator->getErrors();

            if ($returnErrors) {
                return $errors;
            }

            if ($jsonResponse) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errors,
                ])->send();
            }

            // non-json error page
            echo view('errors/html/error_general', [
                'heading' => '400 Bad Request',
                'message' => t('something_went_wrong'),
            ]);
            exit;
        }

        return true;
    }
}