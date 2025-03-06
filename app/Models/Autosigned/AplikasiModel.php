<?php

namespace App\Models\Autosigned;

use CodeIgniter\Model;

class AplikasiModel extends Model
{
    protected $table = 'aplikasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_aplikasi'];
}
