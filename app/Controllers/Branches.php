<?php

namespace App\Controllers;

use App\Models\BranchModel;

class Branches extends BaseAppController
{
    protected BranchModel $branches;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->branches = new BranchModel();
    }

    public function index()
    {
        $data = [
            'branches' => $this->branches->findAll(),
            'title'    => 'Manage Branches'
        ];
        return $this->render('branches/index', $data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->branches->insert([
            'name' => $this->request->getPost('name'),
        ]);

        return redirect()->to('branches')->with('message', 'Branch created successfully.');
    }

    public function delete(int $id)
    {
        $this->branches->delete($id);
        return redirect()->to('branches')->with('message', 'Branch deleted successfully.');
    }
}
