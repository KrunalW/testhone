<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Entities\User;

class UserController extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('admin.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $users = model('UserModel')
            ->select('users.*, auth_groups_users.group')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
            ->findAll();

        $data = [
            'title' => 'Manage Users',
            'users' => $users
        ];

        return view('admin/users/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('admin.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Create User'
        ];

        return view('admin/users/create', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('admin.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'username' => 'required|alpha_numeric|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[user,exam_expert,admin,superadmin]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $users = auth()->getProvider();

        $user = new User([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);

        $users->save($user);

        // Add user to group
        $role = $this->request->getPost('role');
        $user->addGroup($role);

        return redirect()->to('/admin/users')->with('success', "User created successfully with role: {$role}");
    }

    public function delete($id)
    {
        if (!auth()->user()->can('admin.access')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Don't allow deleting yourself
        if ($id == auth()->user()->id) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot delete yourself']);
        }

        $userModel = model('UserModel');
        $user = $userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        if ($userModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
        }
    }
}
