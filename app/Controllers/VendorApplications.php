<?php

namespace App\Controllers;

use App\Models\VendorApplicationModel;
use App\Models\ActivityLogModel;
use App\Models\FsUserModel;

class VendorApplications extends BaseAppController
{
    protected VendorApplicationModel $applicationModel;

    public function __construct()
    {
        $this->applicationModel = new VendorApplicationModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? 'pending';

        $applications = $this->applicationModel
            ->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->render('vendor_applications/index', [
            'applications' => $applications,
            'currentStatus' => $status
        ]);
    }

    public function show($id)
    {
        $application = $this->applicationModel->find($id);

        if (!$application) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('vendor_applications/show', [
            'application' => $application
        ]);
    }

    public function approve($id)
    {
        $application = $this->applicationModel->find($id);

        if (!$application) {
            return $this->response->setJSON(['success' => false, 'message' => 'Application not found.']);
        }

        if ($application['status'] !== 'pending') {
             return $this->response->setJSON(['success' => false, 'message' => 'Application is not pending.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Check if email already exists in users
        $existingUser = $db->table('fs_users')->where('email', $application['email'])->get()->getRow();
        
        $userId = null;
        if (!$existingUser) {
             // 2. Create FsUserModel
             // Auto-generate a password, they will need to reset it
             $tempPassword = bin2hex(random_bytes(8));
             
             // Split contact name
             $nameParts = explode(' ', $application['contact_name'], 2);
             $firstName = $nameParts[0];
             $lastName = $nameParts[1] ?? '';

             $db->table('fs_users')->insert([
                 'first_name' => $firstName,
                 'last_name' => $lastName,
                 'email' => $application['email'],
                 'password_hash' => password_hash($tempPassword, PASSWORD_BCRYPT),
                 'phone' => $application['phone'],
                 'status' => 'active',
                 'created_at' => date('Y-m-d H:i:s'),
                 'updated_at' => date('Y-m-d H:i:s'),
             ]);

             $userId = $db->insertID();

             // 3. Assign Role (subcontractor_vendor)
             $role = $db->table('roles')->where('slug', 'subcontractor_vendor')->get()->getRow();
             if ($role) {
                 $db->table('user_roles')->insert([
                     'user_id' => $userId,
                     'role_id' => $role->id
                 ]);
             }
        } else {
            $userId = $existingUser->id;
        }

        // 4. Update Application
        $this->applicationModel->update($id, [
            'status' => 'approved',
            'user_id' => $userId
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
             return $this->response->setJSON(['success' => false, 'message' => 'Failed to approve application.']);
        }

        // Ideally send an email here with their temp password / password reset link

        return $this->response->setJSON(['success' => true, 'message' => 'Application approved and user account created.']);
    }

    public function reject($id)
    {
        $application = $this->applicationModel->find($id);

        if (!$application) {
             return $this->response->setJSON(['success' => false, 'message' => 'Application not found.']);
        }

        $this->applicationModel->update($id, [
            'status' => 'rejected'
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Application rejected.']);
    }
}
