<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\ArticleDistributionModel;
use App\Models\CategoryPermissionModel;

class ArticleController extends BaseController
{
    protected $articleModel;
    protected $distributionModel;
    protected $categoryPermissionModel;
    protected $db;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->distributionModel = new ArticleDistributionModel();
        $this->categoryPermissionModel = new CategoryPermissionModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $user = user();
        $userData = $this->db->table('users')->where('id', $user->id)->get()->getRowArray();

        // Admin melihat semua artikel

        $data = [
            'title' => 'Article List',
            'articles' => $this->articleModel->getAllArticlesWithDistributions()
        ];
        return view('admin/article/index', $data);


        // Pengguna biasa hanya melihat artikel mereka berdasarkan hierarki
        $data = [
            'title' => 'Articles',
            'articles' => $this->articleModel->getArticlesForUsers($userData)
        ];
        return view('pages/article/index', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required',
            'author' => 'required',
            'image' => 'uploaded[image]|is_image[image]|max_size[image,2048]',
            'category_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Get selected category IDs
            $categoryIds = $this->request->getPost('category_id');

            // Validate access for each selected category
            $userId = user()->id;
            foreach ($categoryIds as $categoryId) {
                $hasAccess = $this->categoryPermissionModel->where([
                    'user_id' => $userId,
                    'category_id' => $categoryId
                ])->first();

                if (!$hasAccess) {
                    throw new \Exception('You do not have permission to use category ID: ' . $categoryId);
                }
            }

            // Save article
            $articleData = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'author' => $this->request->getPost('author'),
                'status' => $this->request->getPost('status', 'draft'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert article and get its ID
            $articleId = $this->articleModel->insert($articleData);

            if (!$articleId) {
                throw new \Exception('Failed to save article');
            }

            // Upload image if exists
            $imageFile = $this->request->getFile('image');
            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $newImageName = $articleId . '.' . $imageFile->getExtension();
                if ($imageFile->move(ROOTPATH . 'public/img/articles', $newImageName)) {
                    $this->articleModel->update($articleId, ['image' => $newImageName]);
                }
            }

            // Save selected categories in the article_categories table
            foreach ($categoryIds as $categoryId) {
                $this->db->table('article_categories')->insert([
                    'article_id' => $articleId,
                    'category_id' => $categoryId
                ]);
            }

            // Proses distribusi
            $distributions = [];

            // Proses direktori
            if ($directorateIds = $this->request->getPost('directorate_ids')) {
                foreach ($directorateIds as $id) {
                    $distributions[] = [
                        'article_id' => $articleId,
                        'target_type' => 'directorate',
                        'target_id' => $id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }

            // Proses divisi
            if ($divisionIds = $this->request->getPost('division_ids')) {
                foreach ($divisionIds as $id) {
                    $distributions[] = [
                        'article_id' => $articleId,
                        'target_type' => 'division',
                        'target_id' => $id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }

            // Proses departemen
            if ($departmentIds = $this->request->getPost('department_ids')) {
                foreach ($departmentIds as $id) {
                    $distributions[] = [
                        'article_id' => $articleId,
                        'target_type' => 'department',
                        'target_id' => $id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }

            // Proses sub departemen
            if ($subDepartmentIds = $this->request->getPost('sub_department_ids')) {
                foreach ($subDepartmentIds as $id) {
                    $distributions[] = [
                        'article_id' => $articleId,
                        'target_type' => 'sub_department',
                        'target_id' => $id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }

            // Simpan distribusi jika ada
            if (!empty($distributions)) {
                if (!$this->distributionModel->insertBatch($distributions)) {
                    throw new \Exception('Failed to save distributions');
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to(base_url('admin/article'))->with('success', 'Article created successfully');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Article Store] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Failed to create article: ' . $e->getMessage());
        }
    }

    private function processHierarchicalDistributions()
    {
        $distributions = [];

        // Dapatkan tingkat distribusi yang dipilih
        $directorateIds = $this->request->getPost('directorate_ids') ?: [];
        $divisionIds = $this->request->getPost('division_ids') ?: [];
        $departmentIds = $this->request->getPost('department_ids') ?: [];
        $subDepartmentIds = $this->request->getPost('sub_department_ids') ?: [];

        // Proses dari level tertinggi hingga terendah
        if (!empty($directorateIds)) {
            // Jika direktorat dipilih, sertakan semua tingkatan di bawahnya
            foreach ($directorateIds as $directorateId) {
                $distributions[] = ['type' => 'directorate', 'id' => $directorateId];
                $this->addEntitiesBelow($directorateId, 'directorate', $distributions);
            }
        } elseif (!empty($divisionIds)) {
            // Jika divisi dipilih, sertakan semua tingkatan di bawahnya
            foreach ($divisionIds as $divisionId) {
                $distributions[] = ['type' => 'division', 'id' => $divisionId];
                $this->addEntitiesBelow($divisionId, 'division', $distributions);
            }
        } elseif (!empty($departmentIds)) {
            // Jika departemen dipilih, sertakan semua sub_departemen di bawahnya
            foreach ($departmentIds as $departmentId) {
                $distributions[] = ['type' => 'department', 'id' => $departmentId];
                $this->addEntitiesBelow($departmentId, 'department', $distributions);
            }
        }

        // Tangani sub_departemen secara mandiri
        if (!empty($subDepartmentIds)) {
            foreach ($subDepartmentIds as $subDepartmentId) {
                $distributions[] = ['type' => 'sub_department', 'id' => $subDepartmentId];
            }
        }

        // Remove duplicates
        return array_values(array_unique($distributions, SORT_REGULAR));
    }

    private function addEntitiesBelow($entityId, $entityType, &$distributions)
    {
        switch ($entityType) {
            case 'directorate':
                // Dapatkan semua divisi di bawah direktorat ini
                $divisions = $this->db->table('divisions')
                    ->where('directorate_id', $entityId)
                    ->get()->getResultArray();

                foreach ($divisions as $division) {
                    $distributions[] = ['type' => 'division', 'id' => $division['id']];

                    // Dapatkan semua departemen di bawah divisi ini
                    $departments = $this->db->table('departments')
                        ->where('division_id', $division['id'])
                        ->get()->getResultArray();

                    foreach ($departments as $department) {
                        $distributions[] = ['type' => 'department', 'id' => $department['id']];

                        // Dapatkan semua sub_departemen di bawah departemen ini
                        $subDepartments = $this->db->table('sub_departments')
                            ->where('department_id', $department['id'])
                            ->get()->getResultArray();

                        foreach ($subDepartments as $subDepartment) {
                            $distributions[] = ['type' => 'sub_department', 'id' => $subDepartment['id']];
                        }
                    }
                }
                break;

            case 'division':
                // Dapatkan semua departemen di bawah divisi ini
                $departments = $this->db->table('departments')
                    ->where('division_id', $entityId)
                    ->get()->getResultArray();

                foreach ($departments as $department) {
                    $distributions[] = ['type' => 'department', 'id' => $department['id']];

                    // Dapatkan semua sub_departemen di bawah departemen ini
                    $subDepartments = $this->db->table('sub_departments')
                        ->where('department_id', $department['id'])
                        ->get()->getResultArray();

                    foreach ($subDepartments as $subDepartment) {
                        $distributions[] = ['type' => 'sub_department', 'id' => $subDepartment['id']];
                    }
                }
                break;

            case 'department':
                // Dapatkan semua sub_departemen di bawah departemen ini
                $subDepartments = $this->db->table('sub_departments')
                    ->where('department_id', $entityId)
                    ->get()->getResultArray();

                foreach ($subDepartments as $subDepartment) {
                    $distributions[] = ['type' => 'sub_department', 'id' => $subDepartment['id']];
                }
                break;
        }
    }

    private function getHierarchyData()
    {
        return [
            'directorates' => $this->db->table('directorates')->select('id, name')->get()->getResultArray(),
            'divisions' => $this->db->table('divisions d')
                ->select('d.id, d.name, d.directorate_id, dir.name as directorate_name')
                ->join('directorates dir', 'dir.id = d.directorate_id')
                ->get()->getResultArray(),
            'departments' => $this->db->table('departments d')
                ->select('d.id, d.name, d.division_id, div.name as division_name')
                ->join('divisions div', 'div.id = d.division_id')
                ->get()->getResultArray(),
            'sub_departments' => $this->db->table('sub_departments sd')
                ->select('sd.id, sd.name, sd.department_id, dept.name as department_name')
                ->join('departments dept', 'dept.id = sd.department_id')
                ->get()->getResultArray()
        ];
    }

    public function view($id)
    {
        // Dapatkan artikel berdasarkan ID
        $article = $this->articleModel->getArticleWithDistributions($id);

        if (empty($article)) {
            return redirect()->to('/pages/home')->with('error', 'Artikel tidak ditemukan.');
        }

        $data = [
            'title' => $article['title'],
            'article' => $article
        ];

        return view('pages/article_detail', $data);
    }

    public function create()
    {
        $userId = user()->id;

        $data = array_merge([
            'title' => 'Create Article',
            'categories' => $this->categoryPermissionModel->getCategoriesForUsers($userId)
        ], $this->getHierarchyData());

        return view('admin/article/create', $data);
    }

    public function edit($id)
    {
        $article = $this->articleModel->getArticleWithDistributions($id);
        if (!$article) {
            return redirect()->back()->with('error', 'Article not found');
        }

        $userId = user()->id;

        $data = array_merge([
            'title' => 'Edit Article',
            'article' => $article,
            'categories' => $this->categoryPermissionModel->getCategoriesForUsers($userId)
        ], $this->getHierarchyData());

        return view('admin/article/edit', $data);
    }

    public function update($id)
    {
        // Aturan validasi untuk artikel
        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required',
            'author' => 'required',
            'status' => 'required'
        ];

        // Jika validasi gagal, kembali ke halaman sebelumnya dengan pesan kesalahan
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Memulai transaksi database
        $this->db->transStart();

        try {
            // Mendapatkan ID pengguna yang sedang login
            $userId = user()->id;

            // Mendapatkan kategori yang dipilih (sekarang dalam bentuk array)
            $categoryIds = $this->request->getPost('category_id');

            // Validasi akses ke setiap kategori yang dipilih
            foreach ($categoryIds as $categoryId) {
                $hasAccess = $this->categoryPermissionModel->where([
                    'user_id' => $userId,
                    'category_id' => $categoryId
                ])->first();

                if (!$hasAccess) {
                    throw new \Exception('You do not have permission to use category ID: ' . $categoryId);
                }
            }

            // Menyiapkan data artikel untuk diupdate
            $articleData = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'author' => $this->request->getPost('author'),
                'status' => $this->request->getPost('status'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Melakukan update artikel
            $success = $this->articleModel->update($id, $articleData);

            if (!$success) {
                throw new \Exception('Failed to update article');
            }

            // Proses gambar jika ada
            $imageFile = $this->request->getFile('image');
            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $currentArticle = $this->articleModel->find($id);
                $oldImage = $currentArticle['image'];

                // Hapus gambar lama jika ada
                if ($oldImage && file_exists(ROOTPATH . 'public/img/articles/' . $oldImage)) {
                    unlink(ROOTPATH . 'public/img/articles/' . $oldImage);
                }

                // Upload gambar baru
                $newImageName = $id . '.' . $imageFile->getExtension();
                $imageFile->move(ROOTPATH . 'public/img/articles/', $newImageName);
                $articleData['image'] = $newImageName; // Simpan nama gambar baru
            }

            // Hapus kategori lama
            $this->db->table('article_categories')->where('article_id', $id)->delete();

            // Simpan kategori baru
            if ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $this->db->table('article_categories')->insert([
                        'article_id' => $id,
                        'category_id' => $categoryId
                    ]);
                }
            }

            // Proses distribusi jika ada
            $distributions = $this->processHierarchicalDistributions();
            $this->distributionModel->updateDistributions($id, $distributions);

            // Menyelesaikan transaksi
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Redirect ke halaman daftar artikel dengan pesan sukses
            return redirect()->to('/admin/article')->with('success', 'Article updated successfully');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            $this->db->transRollback();
            log_message('error', '[Article Update] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update article: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {

        $this->articleModel->delete($id);
        $this->distributionModel->deleteByArticleId($id);
        return redirect()->to('/admin/article')->with('success', 'Article deleted successfully');
    }
}
