<?php

namespace App\Models\Hirarki;

use CodeIgniter\Model;

class DirectorateModel extends Model
{
    protected $table = 'directorates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];
}
