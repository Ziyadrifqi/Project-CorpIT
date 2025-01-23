<?php

namespace App\Controllers;

class User extends BaseController
{
    protected $db;

    public function __construct()
    {
        // Memuat database
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = user()->id;

        // Ambil data user dari tabel 'users'
        $user = $this->db->table('users')->getWhere(['id' => $userId])->getRow();

        $data['title'] = 'My Profile';
        $data['user'] = $user;


        return view('user/index', $data);
    }

    public function updateProfile()
    {
        $userId = user()->id;

        // Ambil data pengguna saat ini
        $user = $this->db->table('users')->getWhere(['id' => $userId])->getRow();

        $data = [
            'username' => $this->request->getPost('username'),
            'fullname' => $this->request->getPost('fullname'),
            'email' => $this->request->getPost('email'),
        ];

        // Cek jika ada file gambar yang di-upload
        if ($this->request->getFile('user_image')->isValid()) {
            $file = $this->request->getFile('user_image');

            // Memindahkan file ke folder img
            $fileName = $file->getRandomName(); // Menghasilkan nama file acak
            $file->move('img', $fileName); // Memindahkan file ke folder img

            // Hapus gambar lama jika ada
            if ($user->user_image && $user->user_image !== 'default.png') {
                // Menghapus file lama dari server
                if (file_exists('img/' . $user->user_image)) {
                    unlink('img/' . $user->user_image); // Hapus file lama
                }
            }

            // Simpan nama file gambar baru di database
            $data['user_image'] = $fileName;
        }

        // Update data pengguna di database
        $this->db->table('users')->update($data, ['id' => $userId]);
        return redirect()->to('/user')->with('success', 'Profile updated successfully');
    }
}
