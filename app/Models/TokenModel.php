<?php

namespace App\Models;

use CodeIgniter\Model;

class TokenModel extends Model
{
    protected $table = 'tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['access_token', 'refresh_token', 'expires_in'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getLatestToken()
    {
        $token = $this->orderBy('created_at', 'DESC')->first();

        if (!$token) {
            log_message('error', 'No token found in database');
            return null;
        }

        // Convert expires_in to timestamp if it isn't already
        if (!is_numeric($token['expires_in'])) {
            $token['expires_in'] = strtotime($token['expires_in']);
        }

        return $token;
    }

    public function saveNewToken($tokenData)
    {
        // Pastikan expires_in integer
        $tokenData['expires_in'] = (int)$tokenData['expires_in'];

        // Cek existing token
        $existingToken = $this->where('refresh_token', $tokenData['refresh_token'])->first();

        if ($existingToken) {
            return $this->update($existingToken['id'], $tokenData);
        } else {
            return $this->insert($tokenData);
        }
    }
}
