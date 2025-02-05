<?php

namespace App\Controllers;

class User extends BaseController
{
    protected $db;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new \App\Models\UserModel();
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
    public function uploadTtd()
    {
        $userId = user()->id;

        // Validate uploaded file
        $validationRules = [
            'signature' => [
                'rules' => 'uploaded[signature]|mime_in[signature,image/png,image/jpg,image/jpeg]|max_size[signature,2048]',
                'errors' => [
                    'uploaded' => 'Please select a file to upload',
                    'mime_in' => 'File type must be PNG, JPG, or JPEG',
                    'max_size' => 'File size must not exceed 2MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError('signature'));
        }

        // Get the uploaded file
        $file = $this->request->getFile('signature');

        // Get current user data
        $user = $this->db->table('users')->getWhere(['id' => $userId])->getRow();

        // Delete old signature if exists
        if ($user->signature && file_exists('img/ttd/' . $user->signature)) {
            unlink('img/ttd/' . $user->signature);
        }

        // Generate new filename
        $fileName = $userId . '_' . date('Ymd_His') . '.' . $file->getExtension();

        // Move file to public/img/ttd directory
        $file->move('img/ttd', $fileName);

        // Update database
        $this->db->table('users')->update(['signature' => $fileName], ['id' => $userId]);

        return redirect()->to('/user')->with('success', 'Signature uploaded successfully');
    }
    public function deleteTtd()
    {
        $userId = user()->id;

        // Get current user data
        $user = $this->db->table('users')->getWhere(['id' => $userId])->getRow();

        // Check if the signature exists and delete it
        if ($user->signature && file_exists('img/ttd/' . $user->signature)) {
            unlink('img/ttd/' . $user->signature);  // Delete the signature file
        }

        // Update the database to remove the signature
        $this->db->table('users')->update(['signature' => null], ['id' => $userId]);

        return redirect()->to('/user')->with('success', 'Signature deleted successfully');
    }
}
