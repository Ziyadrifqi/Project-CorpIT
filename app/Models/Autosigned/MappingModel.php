<?php

namespace App\Models\Autosigned;

use CodeIgniter\Model;

class MappingModel extends Model
{
    protected $table = 'mapping';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_user', 'aplikasi_id'];
}
