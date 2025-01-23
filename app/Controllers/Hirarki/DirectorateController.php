<?php

namespace App\Controllers\Hirarki;

use App\Controllers\BaseController;
use App\Models\Hirarki\DirectorateModel;

class DirectorateController extends BaseController
{
    protected $directorateModel;

    public function __construct()
    {
        $this->directorateModel = new DirectorateModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Directorates List',
            'directorate' => $this->directorateModel->findAll()
        ];
        return view('admin/hirarki/directorate/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create New Directorate'
        ];
        return view('admin/hirarki/directorate/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name')
        ];

        $this->directorateModel->insert($data);
        return redirect()->to('/admin/hirarki/directorate')->with('success', 'Directorate added successfully!');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Directorate',
            'directorate' => $this->directorateModel->find($id)
        ];

        if (!$data['directorate']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Directorate dengan ID $id tidak ditemukan");
        }

        return view('admin/hirarki/directorate/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name')
        ];

        $this->directorateModel->update($id, $data);
        return redirect()->to('/admin/hirarki/directorate')->with('success', 'Directorate updated Succesfully');
    }

    public function delete($id)
    {
        $directorate = $this->directorateModel->find($id);
        if ($directorate) {
            $this->directorateModel->delete($id);
            return redirect()->to('/admin/hirarki/directorate')->with('success', 'Directorate Deleted Succesfully');
        }
        return redirect()->to('/admin/hirarki/directorate')->with('error', 'Directorate tidak ditemukan');
    }
}
