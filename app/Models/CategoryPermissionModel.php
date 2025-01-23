<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryPermissionModel extends Model
{
    protected $table = 'category_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'category_id'];

    public function getCategoriesForUser($userId)
    {
        return $this->where('user_id', $userId)
            ->findColumn('category_id') ?? [];
    }

    public function getCategoriesForUsers($userId)
    {
        return $this->db->table('categories')
            ->select('categories.id, categories.name')
            ->join('category_permissions', 'categories.id = category_permissions.category_id')
            ->where('category_permissions.user_id', $userId)
            ->get()
            ->getResultArray();
    }

    public function getCategoryPermissionsWithDetails()
    {
        $subquery = $this->db->table('category_permissions')
            ->select('user_id, GROUP_CONCAT(category_id) as category_ids')
            ->groupBy('user_id');

        return $this->select('users.id, users.username, GROUP_CONCAT(categories.name SEPARATOR ", ") as categories')
            ->join('users', 'users.id = category_permissions.user_id')
            ->join('categories', 'categories.id = category_permissions.category_id')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->where('auth_groups_users.group_id', 1)
            ->groupBy('users.id, users.username')
            ->findAll();
    }
}
