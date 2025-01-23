<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'email',
        'username',
        'fullname',
        'active'
    ];

    // Fungsi untuk mendapatkan semua user aktif
    public function getAllhistoryUsers()
    {
        return $this->select('users.*')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->where('users.active', 1)
            ->where('auth_groups_users.group_id', 1)
            ->orderBy('users.username', 'ASC')
            ->findAll();
    }

    public function getAdminUsers()
    {
        return $this->select('users.*')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->where('auth_groups_users.group_id', 1)
            ->findAll();
    }

    public function getAdminUserById($userId)
    {
        return $this->select('users.*')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->where('users.id', $userId)
            ->where('auth_groups_users.group_id', 1) // 1 untuk admin
            ->first();
    }

    // Fungsi untuk mendapatkan user berdasarkan ID
    public function getUserById($id)
    {
        return $this->find($id);
    }

    // Fungsi untuk mencari user berdasarkan username atau email
    public function searchUsers($keyword)
    {
        return $this->like('username', $keyword)
            ->orLike('email', $keyword)
            ->findAll();
    }
}
