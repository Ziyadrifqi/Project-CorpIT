<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestVisitorModel extends Model
{
    protected $table = 'guest_visitors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['guest_name', 'guest_id', 'user_id', 'status', 'phone', 'valid_until'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getGuestsWithUserInfo()
    {
        return $this->select('guest_visitors.*, users.username as created_by')
            ->join('users', 'users.id = guest_visitors.user_id')
            ->orderBy('guest_visitors.created_at', 'DESC')
            ->findAll();
    }
}
