<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'auth_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description'];
}
