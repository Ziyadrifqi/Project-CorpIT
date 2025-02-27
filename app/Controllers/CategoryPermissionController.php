<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryPermissionModel;
use App\Models\CategoryModel;
use App\Models\GroupUserModel;
use App\Models\UserModel;

class CategoryPermissionController extends BaseController
{
    protected $categoryPermissionModel;
    protected $categoryModel;
    protected $GroupUserModel;
    protected $UserModel;

    public function __construct()
    {
        $this->categoryPermissionModel = new CategoryPermissionModel();
        $this->categoryModel = new CategoryModel();
        $this->GroupUserModel = new GroupUserModel();
        $this->UserModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Category User Permissions List',
            'categoryPermissions' => $this->categoryPermissionModel->getCategoryPermissionsWithDetails(),
            'users' => $this->UserModel->getAdminUsers(),
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('admin/categories/category_permissions/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Category User Permission',
            'users' => $this->UserModel->getAdminUsers(),
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('admin/categories/category_permissions/create', $data);
    }

    public function store()
    {
        $userId = $this->request->getPost('user_id');
        $categories = $this->request->getPost('categories') ?? [];

        // Delete existing permissions for this user
        $this->categoryPermissionModel->where('user_id', $userId)->delete();

        // Insert new permissions
        foreach ($categories as $categoryId) {
            $this->categoryPermissionModel->insert([
                'user_id' => $userId,
                'category_id' => $categoryId
            ]);
        }

        session()->setFlashdata('success', 'Category permissions updated successfully!');
        return redirect()->to('/admin/category-permissions');
    }


    public function edit($userId)
    {
        // Get user details with role check
        $user = $this->UserModel->getAdminUserById($userId); // Gunakan method baru

        if (!$user) {
            session()->setFlashdata('error', 'User not found or unauthorized');
            return redirect()->to('/admin/category-permissions');
        }

        // Get user's existing permissions
        $existingPermissions = $this->categoryPermissionModel->getCategoriesForUser($userId);

        $data = [
            'title' => 'Edit Category User Permissions',
            'user' => $user,
            'categories' => $this->categoryModel->findAll(),
            'selectedCategories' => $existingPermissions,
            'users' => $this->UserModel->getAdminUsers(),
        ];

        return view('admin/categories/category_permissions/edit', $data);
    }

    public function update($userId)
    {
        // Validate user exists and is admin
        $user = $this->UserModel->getAdminUserById($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found or unauthorized');
        }

        // Get selected categories
        $categories = $this->request->getPost('categories') ?? [];

        // Begin transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete existing permissions
            $this->categoryPermissionModel->where('user_id', $userId)->delete();

            // Insert new permissions
            foreach ($categories as $categoryId) {
                $this->categoryPermissionModel->insert([
                    'user_id' => $userId,
                    'category_id' => $categoryId
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()
                    ->with('error', 'Failed to update permissions. Please try again.');
            }

            return redirect()->to('/admin/category-permissions')
                ->with('success', 'Category permissions updated successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating permissions: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating permissions.');
        }
    }
    // This method should handle the DELETE route
    public function delete($userId)
    {
        // Begin transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Instead of finding a single permission, get all permissions for this user
            $permissions = $this->categoryPermissionModel->where('user_id', $userId)->findAll();
            if (empty($permissions)) {
                return redirect()->back()
                    ->with('error', 'No permissions found for this user');
            }

            // Delete all permissions for this user
            if (!$this->categoryPermissionModel->where('user_id', $userId)->delete()) {
                throw new \Exception('Failed to delete permissions');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()
                    ->with('error', 'Failed to delete permissions. Please try again.');
            }

            return redirect()->to('/admin/category-permissions')
                ->with('success', 'Category permissions deleted successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error deleting category permissions: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the permissions: ' . $e->getMessage());
        }
    }
}
