<?php

namespace App\Models\Hirarki;

use CodeIgniter\Model;

class SubdepartModel extends Model
{
    protected $table = 'sub_departments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'department_id'];
}
