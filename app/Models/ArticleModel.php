<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'content', 'author', 'status', 'image', 'type', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getArticleWithDistributions(int $id): array
    {
        $builder = $this->db->table('articles a');
        $builder->select('a.*, GROUP_CONCAT(c.id) AS category_ids, GROUP_CONCAT(c.name) AS category_names')
            ->join('article_categories ac', 'a.id = ac.article_id', 'left')
            ->join('categories c', 'ac.category_id = c.id', 'left')
            ->where('a.id', $id)
            ->groupBy('a.id');

        $article = $builder->get()->getRowArray();

        if ($article) {
            $article['distributions'] = $this->getDistributionsByArticleId($id);
            $article = $this->organizeDistributions($article, $article['distributions']);
            // Tambahkan ini untuk mengonversi category_ids dari string ke array
            $article['category_id'] = explode(',', $article['category_ids']);
        }

        return $article ?: [];
    }

    public function getAllArticlesWithDistributions(): array
    {
        $builder = $this->db->table('articles a');
        $builder->select('a.*, GROUP_CONCAT(c.name) AS category_names')
            ->join('article_categories ac', 'ac.article_id = a.id', 'left')
            ->join('categories c', 'c.id = ac.category_id', 'left')
            ->groupBy('a.id')
            ->orderBy('a.created_at', 'DESC');

        // Ambil semua artikel jika user login
        if (logged_in()) {
            $articles = $builder->get()->getResultArray();
        } else {
            // Jika tidak login, hanya ambil artikel public yang published
            $articles = $builder->where('a.type', 'public')
                ->where('a.status', 'published')
                ->get()->getResultArray();
        }

        // Ambil distribusi hanya untuk artikel internal
        foreach ($articles as &$article) {
            if ($article['type'] === 'internal') {
                $article['distributions'] = $this->getDistributionsByArticleId($article['id']);
            } else {
                $article['distributions'] = [];
            }
        }

        return $articles;
    }

    private function getDistributionsByArticleId(int $id): array
    {
        return $this->db->table('article_distributions')
            ->where('article_id', $id)
            ->get()
            ->getResultArray();
    }

    private function organizeDistributions(array $article, array $distributions): array
    {
        $article['directorate_ids'] = [];
        $article['division_ids'] = [];
        $article['department_ids'] = [];
        $article['sub_department_ids'] = [];

        foreach ($distributions as $dist) {
            switch ($dist['target_type']) {
                case 'directorate':
                    $article['directorate_ids'][] = $dist['target_id'];
                    break;
                case 'division':
                    $article['division_ids'][] = $dist['target_id'];
                    break;
                case 'department':
                    $article['department_ids'][] = $dist['target_id'];
                    break;
                case 'sub_department':
                    $article['sub_department_ids'][] = $dist['target_id'];
                    break;
            }
        }

        return $article;
    }


    public function getArticlesForUser(?array $userData = null): array
    {
        $builder = $this->db->table('articles a');
        $builder->select('a.*, GROUP_CONCAT(DISTINCT c.id) as category_ids, GROUP_CONCAT(DISTINCT c.name) AS category_names');
        $builder->join('article_categories ac', 'a.id = ac.article_id', 'left');
        $builder->join('categories c', 'ac.category_id = c.id', 'left');

        if ($userData) {
            // User sudah login, gunakan kondisi akses berdasarkan grup
            $builder->join('article_distributions ad', 'a.id = ad.article_id', 'left');
            $conditions = $this->buildUserConditions($userData);
            if (!empty($conditions)) {
                $builder->where('(' . implode(' OR ', $conditions) . ')');
            }
        } else {
            // User belum login, hanya tampilkan artikel public
            $builder->where('a.type', 'public');
        }

        $builder->where('a.status', 'published');
        $builder->groupBy('a.id');
        $builder->orderBy('a.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    private function buildUserConditions(array $userData): array
    {
        $conditions = [];

        if (!empty($userData['directorate_id'])) {
            $conditions[] = "(ad.target_type = 'directorate' AND ad.target_id = {$userData['directorate_id']})";
        }

        if (!empty($userData['division_id'])) {
            $conditions[] = "(
                (ad.target_type = 'division' AND ad.target_id = {$userData['division_id']})
                OR (ad.target_type = 'directorate' AND ad.target_id = {$userData['directorate_id']})
            )";
        }

        if (!empty($userData['department_id'])) {
            $conditions[] = "(
                (ad.target_type = 'department' AND ad.target_id = {$userData['department_id']})
                OR (ad.target_type = 'division' AND ad.target_id = {$userData['division_id']})
                OR (ad.target_type = 'directorate' AND ad.target_id = {$userData['directorate_id']})
            )";
        }

        if (!empty($userData['sub_department_id'])) {
            $conditions[] = "(
                (ad.target_type = 'sub_department' AND ad.target_id = {$userData['sub_department_id']})
                OR (ad.target_type = 'department' AND ad.target_id = {$userData['department_id']})
                OR (ad.target_type = 'division' AND ad.target_id = {$userData['division_id']})
                OR (ad.target_type = 'directorate' AND ad.target_id = {$userData['directorate_id']})
            )";
        }

        return $conditions;
    }

    // Save a new article
    public function saveArticle(array $data): bool
    {
        return $this->save($data);
    }

    // Update an article
    public function updateArticle(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    // Delete an article
    public function deleteArticle(int $id): bool
    {
        return $this->delete($id);
    }
}
