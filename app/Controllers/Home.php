<?php

namespace App\Controllers;

use App\Models\GroupUserModel;
use App\Models\MenuModel;

class Home extends BaseController
{
    protected $db;
    protected $groupUserModel;

    public function __construct()
    {
        // Memuat database
        $this->db = \Config\Database::connect();
        $this->groupUserModel = new GroupUserModel();
    }

    public function index()
    {
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }
    public function user()
    {
        // Ambil ID pengguna saat ini
        $userId = user()->id;

        // Ambil data pengguna dari tabel users
        $userData = $this->db->table('users')
            ->where('id', $userId)
            ->get()
            ->getRow();

        // Ambil group_id pengguna dari auth_groups_users menggunakan model
        $group = $this->groupUserModel->where('user_id', $userId)->first();

        // Pastikan data group ditemukan
        if ($group) {
            $groupId = $group['group_id'];

            // Dapatkan menu berdasarkan role_id atau group_id pengguna
            $menuModel = new MenuModel();
            $menus = $menuModel->getMenusByRole($groupId);  // Menggunakan group_id untuk mendapatkan menu

            // Menyusun data yang akan dikirim ke view
            $data = [
                'title' => 'Home',
                'user' => $userData,
                'menus' => $menus // Menambahkan menu ke data
            ];

            // Jika group_id adalah 2, arahkan ke pages/home dan tampilkan menu
            if ($groupId == 2) {
                return view('template/index', $data);
            } else {
                // Arahkan pengguna lain (selain group_id 2) ke user/index dengan menu yang relevan
                return view('user/index', $data);
            }
        } else {
            // Jika group_id tidak ditemukan, arahkan ke halaman default
            return redirect()->to('/login')->with('error', 'Group pengguna tidak ditemukan');
        }
    }
}
