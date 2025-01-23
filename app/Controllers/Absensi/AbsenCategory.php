<?php

namespace App\Controllers\Absensi;

use App\Controllers\BaseController;
use App\Models\Absensi\AbsenCategoryModel;

class AbsenCategory extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new AbsenCategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Attendance Categories List',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('Absensi/categories/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Attendance Category'
        ];

        return view('Absensi/categories/create', $data);
    }

    public function store()
    {
        $this->categoryModel->save([
            'name' => $this->request->getPost('name'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Category added successfully!');
        return redirect()->to('/Absensi/categories');
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Category with ID $id not found");
        }

        $data = [
            'title' => 'Edit Attendance Category',
            'category' => $category,
            'validation' => \Config\Services::validation()
        ];

        return view('Absensi/categories/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->categoryModel->update($id, [
            'name' => $this->request->getPost('name')
        ]);

        session()->setFlashdata('success', 'Category updated successfully!');
        return redirect()->to('/Absensi/categories');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            session()->setFlashdata('error', 'Category not found!');
            return redirect()->to('/Absensi/categories');
        }

        $this->categoryModel->delete($id);
        session()->setFlashdata('success', 'Category deleted successfully!');
        return redirect()->to('/Absensi/categories');
    }
}
