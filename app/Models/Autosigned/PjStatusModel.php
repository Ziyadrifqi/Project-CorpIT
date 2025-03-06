<?php

namespace App\Models\Autosigned;

use CodeIgniter\Model;

class PjStatusModel extends Model
{
    protected $table = 'pj_status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['mapping_id', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $updatedField = '';

    public function getAllPjAssignments()
    {
        $builder = $this->db->table('pj_status as ps');
        $builder->select('a.nama_aplikasi, m.nama_user, ps.created_at');
        $builder->join('mapping as m', 'm.id = ps.mapping_id');
        $builder->join('aplikasi as a', 'a.id = m.aplikasi_id');
        $builder->where('ps.status', 1);
        $builder->orderBy('ps.created_at', 'DESC');
        $query = $builder->get();

        return $query->getResultArray();
    }
}
