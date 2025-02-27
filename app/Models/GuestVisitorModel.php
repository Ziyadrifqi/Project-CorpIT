<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestVisitorModel extends Model
{
    protected $table = 'guest_visitors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['guest_name', 'user_id', 'status', 'phone', 'email', 'valid_until', 'password'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getGuestsByUserId($userId)
    {
        return $this->db->table('guest_visitors')
            ->select('guest_visitors.*, users.fullname as created_by')
            ->join('users', 'users.id = guest_visitors.user_id', 'left')
            ->where('guest_visitors.user_id', $userId)
            ->orderBy('guest_visitors.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
