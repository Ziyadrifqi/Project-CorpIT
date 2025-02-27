<?php

namespace App\Models;

use CodeIgniter\Model;

class SignPdfModel extends Model
{
    protected $table = 'sign_pdf';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'periode',
        'name_pdf'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    // Fungsi baru untuk get data dengan filter
    public function getSignedDocuments($selectedMonth = null, $selectedUser = null)
    {
        $builder = $this->builder();
        $builder->select('sign_pdf.*, users.fullname');
        $builder->join('users', 'users.id = sign_pdf.user_id');

        if ($selectedMonth) {
            $builder->where('periode', $selectedMonth);
        }

        if ($selectedUser && $selectedUser !== 'all') {
            $builder->where('user_id', $selectedUser);
        }

        return $builder->get()->getResultArray();
    }

    public function savePdfRecord($userId, $periode, $namePdf)
    {
        return $this->insert([
            'user_id' => $userId,
            'periode' => $periode,
            'name_pdf' => $namePdf
        ]);
    }

    public function getPdfsByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
}
