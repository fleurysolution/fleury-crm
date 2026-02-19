<?php

namespace App\Controllers;

use App\Models\FsUserModel;
use App\Models\RoleModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new FsUserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'stats' => [
                'total_users' => $this->userModel->countAllResults(),
                'active_users' => $this->userModel->where('status', 'active')->countAllResults(),
                'total_roles' => $this->roleModel->countAllResults(),
                // Placeholder for future metrics
                'leads' => 0, 
                'projects' => 0
            ]
        ];

        return view('dashboard/index', $data);
    }
}
