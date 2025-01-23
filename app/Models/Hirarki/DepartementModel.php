<?php

namespace App\Models\Hirarki;

use CodeIgniter\Model;

class DepartementModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'division_id'];
}
