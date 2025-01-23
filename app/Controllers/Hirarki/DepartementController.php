<?php

namespace App\Controllers\Hirarki;

use App\Controllers\BaseController;
use App\Models\Hirarki\DivisionModel;
use App\Models\Hirarki\DepartementModel;

class DepartementController extends BaseController
{
    protected $divisionModel;
    protected $departementModel;

    public function __construct()
    {

        $this->departementModel = new DepartementModel();
        $this->divisionModel = new DivisionModel();
    }

    public function index()
    {

        $departements = $this->departementModel->findAll();
        $data = [
            'title' => 'Departement List',
            'departements' => $departements
        ];
        return view('admin/hirarki/departement/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create New Departement',
            'division' => $this->divisionModel->findAll()
        ];
        return view('admin/hirarki/departement/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]',
            'division_id' => 'required|numeric|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'division_id' => $this->request->getPost('division_id'),
        ];

        $this->departementModel->insert($data);
        return redirect()->to('/admin/hirarki/departement')->with('success', 'Departement Add Succesfully');
    }

    public function edit($id)
    {
        $departement = $this->departementModel->find($id);

        if (!$departement) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Departement dengan ID $id tidak ditemukan"
            );
        }

        $data = [
            'title' => 'Edit Departement',
            'departement' => $departement,
            'division' => $this->divisionModel->findAll()
        ];

        return view('admin/hirarki/departement/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]',
            'division_id' => 'required|numeric|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'division_id' => $this->request->getPost('division_id'),
        ];

        $this->departementModel->update($id, $data);
        return redirect()->to('/admin/hirarki/departement')->with('success', 'Departement updated succesfully');
    }

    public function delete($id)
    {
        $departement = $this->departementModel->find($id);
        if ($departement) {
            $this->departementModel->delete($id);
            return redirect()->to('/admin/hirarki/departement')->with('success', 'Departement deleted succesfully');
        }
        return redirect()->to('/admin/hirarki/departement')->with('error', 'Departement tidak ditemukan');
    }
}
