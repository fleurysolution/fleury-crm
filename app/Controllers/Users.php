<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;

class Users extends BaseAppController
{
    /**
     * GET /users — user list (admin)
     */
    public function index(): string
    {
        $db    = \Config\Database::connect();
        $users = $db->query(
            'SELECT u.id, CONCAT(u.first_name, " ", u.last_name) AS name, u.email, u.phone, u.status, u.last_login_at AS last_login,
                    (SELECT r.name FROM roles r JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = u.id LIMIT 1) AS role_name
             FROM fs_users u
             WHERE u.deleted_at IS NULL
             ORDER BY u.first_name ASC, u.last_name ASC'
        )->getResultArray();


        return $this->render('users/index', ['users' => $users]);
    }

    /**
     * GET /users/create — new user form
     */
    public function create(): string
    {
        $roles = \Config\Database::connect()
            ->table('roles')->get()->getResultArray();

        return $this->render('users/create', ['roles' => $roles]);
    }

    /**
     * POST /users/store — create new user
     */
    public function store(): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $db    = \Config\Database::connect();
        $email = trim($this->request->getPost('email') ?? '');

        if ($db->table('fs_users')->where('email', $email)->where('deleted_at IS NULL', null, false)->countAllResults()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Email already in use.']);
            }
            return redirect()->back()->with('error', 'Email already in use.');
        }

        $db->table('fs_users')->insert([
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $email,
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'phone'      => $this->request->getPost('phone'),
            'status'     => $this->request->getPost('status') ?: 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $newId = $db->insertID();
        $fullName = trim($this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'));
        ActivityLogModel::log('user', $newId, 'created', "User {$fullName} created");

        if ($roleId = $this->request->getPost('role_id')) {
            $db->table('user_roles')->insert(['user_id' => $newId, 'role_id' => $roleId]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'id' => $newId]);
        }
        return redirect()->to(site_url('users'))->with('success', 'User created successfully.');
    }

    /**
     * GET /users/:id — view/edit user profile
     */
    public function show(int $id): string
    {
        $db   = \Config\Database::connect();
        $user = $db->query(
            'SELECT u.*, CONCAT(u.first_name, " ", u.last_name) AS name, 
                    (SELECT r.name FROM roles r JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = u.id LIMIT 1) AS role_name,
                    (SELECT ur.role_id FROM user_roles ur WHERE ur.user_id = u.id LIMIT 1) AS role_id
             FROM fs_users u 
             WHERE u.id=? AND u.deleted_at IS NULL',
            [$id]
        )->getRow('array');

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $roles = $db->table('roles')->get()->getResultArray();
        return $this->render('users/show', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * POST /users/:id/update — update user
     */
    public function update(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $db   = \Config\Database::connect();
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => $this->request->getPost('phone'),
            'status'     => $this->request->getPost('status') ?: 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('fs_users')->where('id', $id)->update($data);

        if ($roleId = $this->request->getPost('role_id')) {
            $db->table('user_roles')->where('user_id', $id)->delete();
            $db->table('user_roles')->insert(['user_id' => $id, 'role_id' => $roleId]);
        }

        ActivityLogModel::log('user', $id, 'updated', 'User profile updated');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'User updated.']);
        }
        return redirect()->to(site_url("users/{$id}"))->with('success', 'User updated.');
    }

    /**
     * POST /users/:id/password — change password for any user (admin)
     */
    public function changePassword(int $id): \CodeIgniter\HTTP\Response
    {
        $db      = \Config\Database::connect();
        $newPass = $this->request->getPost('new_password') ?? '';

        if (strlen($newPass) < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        }

        $db->table('fs_users')->where('id', $id)->update([
            'password_hash' => password_hash($newPass, PASSWORD_BCRYPT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        ActivityLogModel::log('user', $id, 'updated', 'Password changed');
        return $this->response->setJSON(['success' => true, 'message' => 'Password updated.']);
    }

    /**
     * POST /users/:id/toggle-status — activate/deactivate
     */
    public function toggleStatus(int $id): \CodeIgniter\HTTP\Response
    {
        $db   = \Config\Database::connect();
        $user = $db->table('fs_users')->where('id', $id)->get()->getRow();
        $new  = ($user->status ?? 'active') === 'active' ? 'inactive' : 'active';
        $db->table('fs_users')->where('id', $id)->update(['status' => $new, 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true, 'status' => $new]);
    }

    /**
     * POST /users/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $db = \Config\Database::connect();
        $db->table('fs_users')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        ActivityLogModel::log('user', $id, 'deleted', 'User deactivated');
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to(site_url('users'))->with('success', 'User removed.');
    }

    // ── My Profile ─────────────────────────────────────────────────────────────

    /**
     * GET /profile — current user's own profile
     */
    public function profile(): string
    {
        $db   = \Config\Database::connect();
        $user = $db->table('fs_users')
            ->select('*, CONCAT(first_name, " ", last_name) AS name')
            ->where('id', $this->currentUser['id'])
            ->get()->getRow('array');

        return $this->render('users/profile', ['user' => $user]);
    }

    /**
     * POST /profile/update — update own profile
     */
    public function updateProfile(): \CodeIgniter\HTTP\Response
    {
        $db  = \Config\Database::connect();
        $uid = $this->currentUser['id'];

        $data = [
            'first_name' => $this->request->getPost('first_name') ?: '',
            'last_name'  => $this->request->getPost('last_name') ?: '',
            'phone'      => $this->request->getPost('phone'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $newEmail = trim($this->request->getPost('email') ?? '');
        $curEmail = $this->currentUser['email'] ?? '';
        if ($newEmail && $newEmail !== $curEmail) {
            $taken = $db->table('fs_users')
                ->where('email', $newEmail)->where('id !=', $uid)->countAllResults();
            if ($taken) {
                return $this->response->setJSON(['success' => false, 'message' => 'Email already in use.']);
            }
            $data['email'] = $newEmail;
        }

        $db->table('fs_users')->where('id', $uid)->update($data);

        // Refresh session user data
        $updated = $db->table('fs_users')->where('id', $uid)->get()->getRow('array');
        session()->set('user', $updated);
        session()->set('user_name', trim(($updated['first_name'] ?? '') . ' ' . ($updated['last_name'] ?? '')));

        return $this->response->setJSON(['success' => true, 'message' => 'Profile updated.']);
    }

    /**
     * POST /profile/password — change own password
     */
    public function changeOwnPassword(): \CodeIgniter\HTTP\Response
    {
        $db      = \Config\Database::connect();
        $uid     = $this->currentUser['id'];
        $current = $this->request->getPost('current_password') ?? '';
        $new     = $this->request->getPost('new_password') ?? '';

        $user = $db->table('fs_users')->where('id', $uid)->get()->getRow();
        if (!password_verify($current, $user->password_hash ?? '')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Current password is incorrect.']);
        }
        if (strlen($new) < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'New password must be at least 6 characters.']);
        }

        $db->table('fs_users')->where('id', $uid)->update([
            'password_hash' => password_hash($new, PASSWORD_BCRYPT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        ActivityLogModel::log('user', $uid, 'updated', 'Own password changed');
        return $this->response->setJSON(['success' => true, 'message' => 'Password changed successfully.']);
    }
}
