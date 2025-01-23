<?php

namespace App\Controllers\Hirarki;

use App\Controllers\BaseController;
use App\Models\Hirarki\DivisionModel;
use App\Models\Hirarki\DirectorateModel;

class DivisionController extends BaseController
{
    protected $divisionModel;
    protected $directorateModel;

    public function __construct()
    {
        $this->divisionModel = new DivisionModel();
        $this->directorateModel = new DirectorateModel();
    }

    public function index()
    {
        $divisions = $this->divisionModel->findAll();

        $data = [
            'title' => 'Divisions List',
            'divisions' => $divisions
        ];
        return view('admin/hirarki/division/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create New Division',
            'directorate' => $this->directorateModel->findAll()
        ];
        return view('admin/hirarki/division/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]',
            'directorate_id' => 'required|numeric|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'directorate_id' => $this->request->getPost('directorate_id'),
        ];

        $this->divisionModel->insert($data);
        return redirect()->to('/admin/hirarki/division')->with('success', 'Division Add Succesfully');
    }

    public function edit($id)
    {
        $division = $this->divisionModel->find($id);

        if (!$division) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Division dengan ID $id tidak ditemukan"
            );
        }

        $data = [
            'title' => 'Edit Division',
            'division' => $division,
            'directorate' => $this->directorateModel->findAll()
        ];

        return view('admin/hirarki/division/edit', $data);
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

        $this->divisionModel->update($id, $data);
        return redirect()->to('/admin/hirarki/division')->with('success', 'Division updated succesfully');
    }

    public function delete($id)
    {
        $division = $this->divisionModel->find($id);
        if ($division) {
            $this->divisionModel->delete($id);
            return redirect()->to('/admin/hirarki/division')->with('success', 'Division deleted succesfully');
        }
        return redirect()->to('/admin/hirarki/division')->with('error', 'Division tidak ditemukan');
    }
}
