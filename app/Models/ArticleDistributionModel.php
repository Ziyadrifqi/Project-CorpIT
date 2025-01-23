<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleDistributionModel extends Model
{
    protected $table = 'article_distributions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['article_id', 'target_type', 'target_id', 'created_at'];

    public function saveDistributions($articleId, $distributions)
    {
        $data = [];
        foreach ($distributions as $distribution) {
            $data[] = [
                'article_id' => $articleId,
                'target_type' => $distribution['type'],
                'target_id' => $distribution['id']
            ];
        }
        return $this->insertBatch($data);
    }

    public function updateDistributions($articleId, $distributions)
    {
        $this->deleteByArticleId($articleId); // Hapus yang lama
        return $this->saveDistributions($articleId, $distributions); // Simpan yang baru
    }

    public function deleteByArticleId($articleId)
    {
        return $this->where('article_id', $articleId)->delete();
    }
}
