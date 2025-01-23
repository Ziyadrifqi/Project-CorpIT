<?php

namespace App\Models\Hirarki;

use CodeIgniter\Model;

class DivisionModel extends Model
{
    protected $table = 'divisions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'directorate_id'];

    public function getDivisionsWithDirectorate()
    {
        return $this->select('divisions.*, directorates.name as directorate_name')
            ->join('directorates', 'divisions.directorate_id = directorates.id')
            ->findAll();
    }
}
