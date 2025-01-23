<?php

namespace App\Controllers;

class Admin extends BaseController
{
    protected $db, $builder;
    protected $validation;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('users');
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data['title'] = 'User List';

        $this->builder->select('users.id as userid, username, email, name');
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        $query = $this->builder->get();

        $data['users'] = $query->getResult();
        return view('admin/index', $data);
    }

    public function detail($id = 0)
    {
        $data['title'] = 'User Detail';

        // Existing user detail query
        $this->builder = $this->db->table('users');
        $this->builder->select('
            users.id as userid, username, email, fullname, initial,
            sub_departments.name as sub_department_name,
            departments.name as department_name,
            divisions.name as division_name,
            directorates.name as directorate_name,
            users.sub_department_id,
            users.department_id,
            users.division_id,
            users.directorate_id,
            position, user_image, auth_groups.name as role,
            auth_groups.id as role_id
        ');
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        $this->builder->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left');
        $this->builder->join('departments', 'departments.id = users.department_id', 'left');
        $this->builder->join('divisions', 'divisions.id = users.division_id', 'left');
        $this->builder->join('directorates', 'directorates.id = users.directorate_id', 'left');
        $this->builder->where('users.id', $id);
        $query = $this->builder->get();

        // Get user data
        $data['user'] = $query->getRow();

        if (empty($data['user'])) {
            return redirect()->to('/admin');
        }

        // Get dropdown data
        $data['sub_departments'] = $this->getSubDepartments();
        $data['departments'] = $this->getDepartments();
        $data['divisions'] = $this->getDivisions();
        $data['directorates'] = $this->getDirectorates();
        $data['roles'] = $this->getRoles();

        return view('admin/detail', $data);
    }

    public function update($id)
    {
        // Validasi input
        $rules = [
            'username' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email',
            'fullname' => 'required',
            'initial' => 'required',
            'sub_department' => 'required|numeric',
            'department' => 'required|numeric',
            'division' => 'required|numeric',
            'directorate' => 'required|numeric',
            'position' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validation->getErrors());
        }

        // Start Transaction
        $this->db->transStart();

        try {
            // Update user data
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'fullname' => $this->request->getPost('fullname'),
                'initial' => $this->request->getPost('initial'),
                'sub_department_id' => $this->request->getPost('sub_department'),
                'department_id' => $this->request->getPost('department'),
                'division_id' => $this->request->getPost('division'),
                'directorate_id' => $this->request->getPost('directorate'),
                'position' => $this->request->getPost('position'),
            ];

            $this->builder->where('id', $id);
            $this->builder->update($userData);

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                // Transaction failed
                return redirect()->back()->withInput()
                    ->with('error', 'Failed to update user details. Please try again.');
            }

            return redirect()->to('/admin/detail/' . $id)
                ->with('success', 'User details updated successfully.');
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();

            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating user details.');
        }
    }

    // Method untuk mendapatkan data roles
    private function getRoles()
    {
        $builder = $this->db->table('auth_groups');
        $builder->select('id, name, description');
        $builder->orderBy('name', 'ASC');
        $query = $builder->get();
        return $query->getResult();
    }

    private function getSubDepartments()
    {
        $builder = $this->db->table('sub_departments');
        $builder->select('id, name');
        $builder->orderBy('name', 'ASC');
        $query = $builder->get();
        return $query->getResult();
    }

    private function getDepartments()
    {
        $builder = $this->db->table('departments');
        $builder->select('id, name');
        $builder->orderBy('name', 'ASC');
        $query = $builder->get();
        return $query->getResult();
    }

    private function getDivisions()
    {
        $builder = $this->db->table('divisions');
        $builder->select('id, name');
        $builder->orderBy('name', 'ASC');
        $query = $builder->get();
        return $query->getResult();
    }

    private function getDirectorates()
    {
        $builder = $this->db->table('directorates');
        $builder->select('id, name');
        $builder->orderBy('name', 'ASC');
        $query = $builder->get();
        return $query->getResult();
    }
}
