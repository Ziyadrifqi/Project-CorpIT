<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GroupModel;

class UserRoleManagement extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data['title'] = 'User Role Management';
        $data['users'] = $this->getUsersWithRoles();

        return view('admin/user_roles', $data);
    }

    private function getUsersWithRoles()
    {
        $builder = $this->db->table('users');
        $builder->select('users.id as userid, username, email, auth_groups.name as role');
        $builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');

        return $builder->get()->getResult();
    }

    public function edit($id = 0)
    {
        $data['title'] = 'Edit User Role';

        // Fetch user data
        $builder = $this->db->table('users');
        $builder->select('users.id as userid, username, email, auth_groups_users.group_id as role_id');
        $builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $builder->where('users.id', $id);
        $userQuery = $builder->get();

        // Ensure you are getting a single row as an object
        $data['user'] = $userQuery->getRow();

        // Fetch all roles
        $roleModel = new GroupModel();
        $data['roles'] = $roleModel->findAll();

        // Check if user data is found
        if (empty($data['user'])) {
            return redirect()->to('/admin/user_roles')->with('error', 'User not found.');
        }

        return view('admin/edit_role', $data);
    }

    public function updateRole()
    {
        $userId = $this->request->getPost('user_id');
        $roleId = $this->request->getPost('role_id');

        // Update the user's role
        $this->db->table('auth_groups_users')
            ->where('user_id', $userId)
            ->update(['group_id' => $roleId]);

        return redirect()->to('/admin/user_roles')->with('success', 'User role updated successfully.');
    }
}
