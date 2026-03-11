<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\BranchModel;

class Departments extends BaseAppController
{
    protected DepartmentModel $departments;
    protected BranchModel $branches;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->departments = new DepartmentModel();
        $this->branches = new BranchModel();
    }

    public function index()
    {
        $data = [
            'departments' => $this->departments->findAll(),
            'branches'    => $this->branches->findAll(),
            'title'       => 'Manage Departments'
        ];
        return $this->render('departments/index', $data);
    }

    public function create()
    {
        $rules = [
            'name'      => 'required|min_length[3]',
            'branch_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->departments->insert([
            'name'      => $this->request->getPost('name'),
            'branch_id' => $this->request->getPost('branch_id'),
        ]);

        return redirect()->to('departments')->with('message', 'Department created successfully.');
    }

    public function delete(int $id)
    {
        $this->departments->delete($id);
        return redirect()->to('departments')->with('message', 'Department deleted successfully.');
    }
}
