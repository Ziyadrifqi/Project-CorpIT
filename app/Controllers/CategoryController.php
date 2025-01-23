<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Categories List',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('admin/categories/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Category'
        ];

        return view('admin/categories/create', $data);
    }

    public function store()
    {
        $this->categoryModel->save([
            'name' => $this->request->getPost('name'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Category added successfully!');
        return redirect()->to('/admin/categories');
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Category with ID $id not found");
        }

        $data = [
            'title' => 'Edit Category',
            'category' => $category,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/categories/edit', $data);
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
        return redirect()->to('/admin/categories');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            session()->setFlashdata('error', 'Category not found!');
            return redirect()->to('/admin/categories');
        }

        $this->categoryModel->delete($id);
        session()->setFlashdata('success', 'Category deleted successfully!');
        return redirect()->to('/admin/categories');
    }
}
