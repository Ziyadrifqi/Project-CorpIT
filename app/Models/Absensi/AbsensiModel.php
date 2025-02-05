<?php

namespace App\Models\Absensi;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['user_id', 'tanggal', 'jam_masuk', 'jam_keluar', 'tanggal_keluar', 'status', 'category_id', 'judul_kegiatan', 'kegiatan_harian', 'no_tiket', 'pbr_tugas', 'nik'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Cek apakah user sudah absen hari ini
    public function checkAbsenHariIni($user_id)
    {
        return $this->select('absensi.*, absen_category.name as category_name')
            ->join('absen_category', 'absen_category.id = absensi.category_id')
            ->where([
                'absensi.user_id' => $user_id,
                'absensi.tanggal' => date('Y-m-d')
            ])
            ->orderBy('absensi.created_at', 'DESC')
            ->first();
    }


    public function resetAbsensiStatus($user_id)
    {
        $lastAbsensi = $this->where([
            'user_id' => $user_id,
            'tanggal' => date('Y-m-d')
        ])->orderBy('created_at', 'DESC')->first();

        // Jika belum ada absensi hari ini atau status terakhir adalah 'pulang', kembalikan true
        if (!$lastAbsensi || $lastAbsensi['status'] == 'pulang') {
            return true;
        }

        return false;
    }
    public function canPreAttend($user_id)
    {
        $lastAbsensi = $this->where([
            'user_id' => $user_id,
            'tanggal' => date('Y-m-d'),
            'status' => 'pending'
        ])->first();

        // Jika belum ada absensi pending hari ini, kembalikan true
        return !$lastAbsensi;
    }

    // Cek pre-absensi hari ini
    public function checkPreAbsensiHariIni($user_id)
    {
        return $this->where([
            'user_id' => $user_id,
            'tanggal' => date('Y-m-d')
        ])->first();
    }

    public function getPendingAbsensi($user_id)
    {
        return $this->select('absensi.*, absen_category.name as category_name')
            ->join('absen_category', 'absen_category.id = absensi.category_id')
            ->where([
                'absensi.user_id' => $user_id,
                'absensi.status !=' => 'pulang'
            ])
            ->orderBy('absensi.tanggal', 'DESC')
            ->first();
    }

    public function getHistory($user_id, $startDate, $endDate, $category_id = null)
    {
        $builder = $this->select('absensi.*, 
                                  absen_category.name as category_name, 
                                  absensi.tanggal_keluar')
            ->join('absen_category', 'absen_category.id = absensi.category_id')
            ->where('absensi.user_id', $user_id)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate);

        // Add category filter if specified
        if (!empty($category_id)) {
            $builder->where('absensi.category_id', $category_id);
        }

        return $builder->orderBy('absensi.tanggal', 'DESC')
            ->findAll();
    }

    public function getSuperAdminHistory($startDate, $endDate, $categoryId = null, $userId = null)
    {
        $builder = $this->select('absensi.*, users.username as user_name, absen_category.name as category_name')
            ->join('users', 'users.id = absensi.user_id')
            ->join('auth_groups_users', 'auth_groups_users.user_id = absensi.user_id')
            ->join('absen_category', 'absen_category.id = absensi.category_id')
            ->where('auth_groups_users.group_id', 1)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate);

        // Optional filter by category
        if (!empty($categoryId)) {
            $builder->where('absensi.category_id', $categoryId);
        }

        // Optional filter by user
        if (!empty($userId) && $userId !== 'all') {
            $builder->where('absensi.user_id', $userId);
        }

        return $builder->orderBy('absensi.tanggal', 'DESC')
            ->findAll();
    }
}
