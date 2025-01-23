<?php

namespace App\Models;

use CodeIgniter\Model;

class FileUploadModel extends Model
{
    protected $table = 'fileuploads';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'author',  'file_path', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getFileWithDistributions(int $id): array
    {
        $builder = $this->db->table('fileuploads a');
        $builder->select('a.*, GROUP_CONCAT(c.id) AS category_id, GROUP_CONCAT(c.name) AS category_names')
            ->join('file_categories ac', 'a.id = ac.fileuploads_id', 'left')
            ->join('categories c', 'ac.category_id = c.id', 'left')
            ->where('a.id', $id)
            ->groupBy('a.id');

        $file = $builder->get()->getRowArray();

        if ($file) {
            $file['distributions'] = $this->getDistributionsByFileId($id);
            $file = $this->organizeDistributions($file, $file['distributions']);
            // Tambahkan ini untuk mengonversi category_ids dari string ke array
            $file['category_id'] = explode(',', $file['category_id']);
        }

        return $file ?: [];
    }
    private function getDistributionsByFileId(int $id): array
    {
        return $this->db->table('file_distributions')
            ->where('fileuploads_id ', $id)
            ->get()
            ->getResultArray();
    }
    private function organizeDistributions(array $file, array $distributions): array
    {
        $file['directorate_ids'] = [];
        $file['division_ids'] = [];
        $file['department_ids'] = [];
        $file['sub_department_ids'] = [];

        foreach ($distributions as $dist) {
            switch ($dist['target_type']) {
                case 'directorate':
                    $file['directorate_ids'][] = $dist['target_id'];
                    break;
                case 'division':
                    $file['division_ids'][] = $dist['target_id'];
                    break;
                case 'department':
                    $file['department_ids'][] = $dist['target_id'];
                    break;
                case 'sub_department':
                    $file['sub_department_ids'][] = $dist['target_id'];
                    break;
            }
        }

        return $file;
    }

    public function getAllFileWithDistributions(): array
    {
        $builder = $this->db->table('fileuploads a');

        // Mengambil file dan nama kategori dengan join ke tabel relasi
        $builder->select('a.*, GROUP_CONCAT(c.name) AS category_names')
            ->join('file_categories ac', 'ac.fileuploads_id = a.id', 'left')
            ->join('categories c', 'c.id = ac.category_id', 'left')
            ->groupBy('a.id') // Mengelompokkan hasil berdasarkan ID artikel
            ->orderBy('a.created_at', 'DESC');

        $files = $builder->get()->getResultArray();

        foreach ($files as &$file) {
            // Mendapatkan distribusi artikel
            $file['distributions'] = $this->getDistributionsByFileId($file['id']);
        }

        return $files;
    }

    public function getFileForUser(array $userData): array
    {
        $builder = $this->db->table('fileuploads a');
        // Select all file fields and concatenate category names
        $builder->select('a.*, GROUP_CONCAT(DISTINCT c.id) as category_id, GROUP_CONCAT(DISTINCT c.name) AS category_names');

        // Join the file_distributions table
        $builder->join('file_distributions ad', 'a.id = ad.fileuploads_id', 'left');

        // Join the file_categories table to get category information
        $builder->join('file_categories ac', 'a.id = ac.fileuploads_id', 'left');
        $builder->join('categories c', 'ac.category_id = c.id', 'left');

        // Build user conditions
        $conditions = $this->buildUserConditions($userData);

        // Apply conditions if they exist
        if (!empty($conditions)) {
            $builder->where('(' . implode(' OR ', $conditions) . ')');
        }

        // Ensure only published file are returned
        $builder->where('a.status', 'published');

        // Group by file ID to aggregate category names
        $builder->groupBy('a.id');
        $builder->orderBy('a.created_at', 'DESC');

        // Execute the query and return results
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
    // Save a new file
    public function saveFile(array $data): bool
    {
        return $this->save($data);
    }

    // Update an file
    public function updateFile(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    // Delete an file
    public function deleteFile(int $id): bool
    {
        return $this->delete($id);
    }
}
