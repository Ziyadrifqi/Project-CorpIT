<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getArticlesByCategoryId($categoryId)
    {
        // Bergabung dengan tabel categories untuk mendapatkan nama kategori
        $builder = $this->table('articles')
            ->select('articles.*, categories.name as category_name') // Menambahkan kategori_name
            ->join('categories', 'categories.id = articles.category_id') // Join dengan tabel categories
            ->where('category_id', $categoryId); // Filter berdasarkan category_id

        return $builder->get()->getResultArray(); // Mengambil hasil sebagai array
    }
}
