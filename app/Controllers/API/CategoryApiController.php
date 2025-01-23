<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryApiController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        // Ambil semua kategori
        $categories = $this->categoryModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function show($id)
    {
        // Cari kategori berdasarkan ID
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "Category with ID $id not found",
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function store()
    {
        $data = $this->request->getJSON(true);

        // Jika JSON kosong, coba ambil dari POST
        if (empty($data)) {
            $data = $this->request->getPost();
        }

        // Validasi input
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[255]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(400);
        }

        $this->categoryModel->save([
            'name' => $data['name'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Category created successfully',
        ]);
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true);

        // Jika JSON kosong, coba ambil dari POST
        if (empty($data)) {
            $data = $this->request->getPost();
        }

        // Validasi input
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[255]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(400);
        }

        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "Category with ID $id not found",
            ])->setStatusCode(404);
        }

        $this->categoryModel->update($id, [
            'name' => $data['name'],
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Category updated successfully',
        ]);
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "Category with ID $id not found",
            ])->setStatusCode(404);
        }

        $this->categoryModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ]);
    }
}
