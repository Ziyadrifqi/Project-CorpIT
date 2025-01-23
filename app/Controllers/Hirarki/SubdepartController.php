<?php

namespace App\Controllers\Hirarki;

use App\Controllers\BaseController;
use App\Models\Hirarki\DepartementModel;
use App\Models\Hirarki\SubdepartModel;

class SubdepartController extends BaseController
{
    protected $departementModel;
    protected $subdepartModel;

    public function __construct()
    {

        $this->departementModel = new DepartementModel();
        $this->subdepartModel = new SubdepartModel();
    }

    public function index()
    {

        $subdeparts = $this->subdepartModel->findAll();
        $data = [
            'title' => 'Sub Departement List',
            'subdeparts' => $subdeparts
        ];
        return view('admin/hirarki/subdepart/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create New Sub Departement',
            'departement' => $this->departementModel->findAll()
        ];
        return view('admin/hirarki/subdepart/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]',
            'department_id' => 'required|numeric|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'department_id' => $this->request->getPost('department_id'),
        ];

        $this->subdepartModel->insert($data);
        return redirect()->to('/admin/hirarki/subdepart')->with('success', 'Sub Departement Add Succesfully');
    }

    public function edit($id)
    {
        $subdepart = $this->subdepartModel->find($id);

        if (!$subdepart) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Sub Departement dengan ID $id tidak ditemukan"
            );
        }

        $data = [
            'title' => 'Edit Departement',
            'subdepart' => $subdepart,
            'departement' => $this->departementModel->findAll()
        ];

        return view('admin/hirarki/subdepart/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]',
            'department_id' => 'required|numeric|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'department_id' => $this->request->getPost('department_id'),
        ];

        $this->subdepartModel->update($id, $data);
        return redirect()->to('/admin/hirarki/subdepart')->with('success', 'Sub Departement updated succesfully');
    }

    public function delete($id)
    {
        $subdepart = $this->subdepartModel->find($id);
        if ($subdepart) {
            $this->subdepartModel->delete($id);
            return redirect()->to('/admin/hirarki/subdepart')->with('success', 'Sub Departement deleted succesfully');
        }
        return redirect()->to('/admin/hirarki/subdepart')->with('error', 'Sub Departement tidak ditemukan');
    }
}
