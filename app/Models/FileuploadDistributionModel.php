<?php

namespace App\Models;

use CodeIgniter\Model;

class FileuploadDistributionModel extends Model
{
    protected $table = 'file_distributions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fileuploads_id', 'target_type', 'target_id', 'created_at'];

    public function saveDistributions($fileId, $distributions)
    {
        $data = [];
        foreach ($distributions as $distribution) {
            $data[] = [
                'fileuploads_id' => $fileId,
                'target_type' => $distribution['type'],
                'target_id' => $distribution['id']
            ];
        }
        return $this->insertBatch($data);
    }

    public function updateDistributions($fileId, $distributions)
    {
        $this->deleteByFileId($fileId); // Hapus yang lama
        return $this->saveDistributions($fileId, $distributions); // Simpan yang baru
    }

    public function deleteByFileId($fileId)
    {
        return $this->where('fileuploads_id', $fileId)->delete();
    }
}
