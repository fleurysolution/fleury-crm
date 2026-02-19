<?php

namespace App\Controllers;

use App\Models\FsUserModel;
use App\Models\RoleModel;

class Team extends BaseController
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
        // Check permission (Basic check for now, ideally use Filter or Policy)
        // Authorization is handled by PermissionFilter on the route

        $data = [
            'title' => 'Team Management',
            'users' => $this->userModel->findAll(), // Pagination should be added for large teams
        ];

        return view('team/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Team Member',
            'roles' => $this->roleModel->findAll(),
        ];

        return view('team/form', $data);
    }

    public function store()
    {
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => 'required|valid_email|is_unique[fs_users.email]',
            'password'   => 'required|min_length[8]',
            'role_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash((string)$this->request->getPost('password'), PASSWORD_DEFAULT),
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        $userId = $this->userModel->insert($userData);

        if ($userId) {
            // Assign Role
            $db = \Config\Database::connect();
            $db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $this->request->getPost('role_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('team')->with('message', 'Team member added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create user.');
    }
    
    // Edit and Delete methods can be added similarly
}
