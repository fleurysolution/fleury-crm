<?php

namespace App\Controllers;

use App\Models\PermissionModel;
use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;
    protected $permissionModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Role Management',
            'roles' => $this->roleModel->findAll(),
        ];
        return view('roles/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Role',
            'permissions' => $this->permissionModel->findAll(),
        ];
        return view('roles/form', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[191]',
            'slug' => 'required|min_length[3]|max_length[191]|is_unique[roles.slug]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleId = $this->roleModel->insert([
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
        ]);

        if ($roleId) {
            // Assign permissions
            $permissions = $this->request->getPost('permissions') ?? [];
            if (!empty($permissions)) {
                $db = \Config\Database::connect();
                $builder = $db->table('role_permissions');
                $data = [];
                foreach ($permissions as $permId) {
                    $data[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permId
                    ];
                }
                $builder->insertBatch($data);
            }

            return redirect()->to('roles')->with('message', 'Role created successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create role.');
    }
}
