<?php

namespace App\Models\Absensi;

use CodeIgniter\Model;

class AbsenCategoryModel extends Model
{
    protected $table = 'absen_category';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
