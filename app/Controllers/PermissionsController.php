<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PermissionModel;

class PermissionsController extends BaseController
{
    protected $permissionModel;

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
    }

    public function index()
    {
        $data['title'] = 'Permissions';
        $data['permissions'] = $this->permissionModel->findAll();
        return view('admin/permissions/index', $data);
    }

    public function create()
    {
        $data['title'] = 'Add Permission';
        return view('admin/permissions/create', $data);
    }

    public function store()
    {
        $this->permissionModel->save([
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
        ]);

        return redirect()->to(base_url('admin/permissions'))->with('success', 'Permission created successfully.');
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Permission'; // Title for the edit page
        $data['permission'] = $this->permissionModel->find($id);
        return view('admin/permissions/edit', $data);
    }

    public function update($id)
    {
        $this->permissionModel->update($id, [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
        ]);

        return redirect()->to(base_url('admin/permissions'))->with('success', 'Permission updated successfully.');
    }

    public function delete($id)
    {
        $this->permissionModel->delete($id);
        return redirect()->to(base_url('admin/permissions'))->with('success', 'Permission deleted successfully.');
    }
}
