<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GroupModel;

class RoleController extends BaseController
{
    protected $groupModel;

    public function __construct()
    {
        $this->groupModel = new GroupModel();
    }

    // Menampilkan daftar role
    public function index()
    {
        $data['roles'] = $this->groupModel->findAll();
        $data['title'] = 'Role List';
        return view('admin/roles/index', $data);
    }

    // Menampilkan form untuk menambah role
    public function create()
    {
        $data['title'] = 'Add Role';
        return view('admin/roles/create', $data);
    }

    // Menyimpan role baru
    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[50]',
            'description' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->to('/admin/roles/create')->withInput()->with('errors', $this->validator->getErrors());
        }

        // Menyimpan data ke database
        $this->groupModel->insert([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/roles')->with('success', 'Role created successfully');
    }

    // Menampilkan form untuk mengedit role
    public function edit($id)
    {
        $data['role'] = $this->groupModel->find($id);
        $data['title'] = 'Edit Role';

        // Cek apakah role ada
        if (empty($data['role'])) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found');
        }

        return view('admin/roles/edit', $data);
    }

    // Memperbarui role
    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[50]',
            'description' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->to('/admin/roles/edit/' . $id)->withInput()->with('errors', $this->validator->getErrors());
        }

        // Memperbarui data role
        $this->groupModel->update($id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/roles')->with('success', 'Role updated successfully');
    }

    // Menghapus role
    public function delete($id)
    {
        // Cek apakah role ada
        if (!$this->groupModel->find($id)) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found');
        }

        $this->groupModel->delete($id);
        return redirect()->to('/admin/roles')->with('success', 'Role deleted successfully');
    }
}
