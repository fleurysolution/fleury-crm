<?php

namespace App\Controllers;

use App\Models\FsPagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class About extends BaseAppController
{
    protected FsPagesModel $pagesModel;

    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        $this->pagesModel = model(FsPagesModel::class);
    }

    public function index(string $slug = '')
    {
        $slug = trim($slug);
        if ($slug === '') {
            throw PageNotFoundException::forPageNotFound();
        }

        $page = $this->pagesModel->getActiveBySlug($slug);
        if (! $page) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Access control
        if ((int)$page->internal_only === 1) {
            if (! $this->loginUser) {
                return redirect()->to(site_url('forbidden'));
            }

            // Admin always allowed
            if (empty($this->loginUser->is_admin)) {
                $userType = $this->loginUser->user_type ?? '';

                if ($page->visible_to === 'staff' && $userType !== 'staff') {
                    return redirect()->to(site_url('forbidden'));
                }

                if ($page->visible_to === 'client' && $userType !== 'client') {
                    return redirect()->to(site_url('forbidden'));
                }
            }
        }

        $viewData = [
            'model_info' => $page,
            'full_width' => (int)$page->layout_full_width === 1,
            'topbar'     => (int)$page->hide_topbar === 1 ? false : null,
        ];

        // If page is NOT internal, keep old public behavior
        if ((int)$page->internal_only === 0) {
            $viewData['topbar']    = ((int)$page->hide_topbar === 1) ? false : 'includes/public/topbar';
            $viewData['left_menu'] = false;
        }

        return view('about/index', $viewData);
    }
}