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
            'type' => 'required|in_list[public,internal]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Get selected category IDs
            $categoryIds = $this->request->getPost('category_id');
            $type = $this->request->getPost('type');

            // Validasi akses kategori hanya jika artikel bertipe internal
            if ($type === 'internal') {
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
            }

            // Save article
            $articleData = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'author' => $this->request->getPost('author'),
                'status' => $this->request->getPost('status', 'draft'),
                'type' => $type,
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

            // Proses distribusi hanya jika artikel bertipe internal
            if ($type === 'internal') {
                $distributions = [];

                // Proses distribusi seperti sebelumnya...
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
        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required',
            'author' => 'required',
            'status' => 'required',
            'type' => 'required|in_list[public,internal]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            $userId = user()->id;
            $categoryIds = $this->request->getPost('category_id');
            $type = $this->request->getPost('type');

            // Validasi akses kategori
            foreach ($categoryIds as $categoryId) {
                $hasAccess = $this->categoryPermissionModel->where([
                    'user_id' => $userId,
                    'category_id' => $categoryId
                ])->first();

                if (!$hasAccess) {
                    throw new \Exception('You do not have permission to use category ID: ' . $categoryId);
                }
            }

            // Data artikel
            $articleData = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'author' => $this->request->getPost('author'),
                'status' => $this->request->getPost('status'),
                'type' => $type,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update artikel
            if (!$this->articleModel->update($id, $articleData)) {
                throw new \Exception('Failed to update article');
            }

            // Proses gambar
            $imageFile = $this->request->getFile('image');
            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $currentArticle = $this->articleModel->find($id);
                if ($currentArticle['image'] && file_exists(ROOTPATH . 'public/img/articles/' . $currentArticle['image'])) {
                    unlink(ROOTPATH . 'public/img/articles/' . $currentArticle['image']);
                }
                $newImageName = $id . '.' . $imageFile->getExtension();
                $imageFile->move(ROOTPATH . 'public/img/articles/', $newImageName);
                $this->articleModel->update($id, ['image' => $newImageName]);
            }

            // Update kategori
            $this->db->table('article_categories')->where('article_id', $id)->delete();
            foreach ($categoryIds as $categoryId) {
                $this->db->table('article_categories')->insert([
                    'article_id' => $id,
                    'category_id' => $categoryId
                ]);
            }

            // Hanya proses distribusi jika tipe internal
            if ($type === 'internal') {
                // Hapus distribusi lama
                $this->db->table('article_distributions')->where('article_id', $id)->delete();

                $distributions = [];

                // Proses distribusi untuk tiap level
                if ($directorateIds = $this->request->getPost('directorate_ids')) {
                    foreach ($directorateIds as $dirId) {
                        $distributions[] = [
                            'article_id' => $id,
                            'target_type' => 'directorate',
                            'target_id' => $dirId,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }

                if ($divisionIds = $this->request->getPost('division_ids')) {
                    foreach ($divisionIds as $divId) {
                        $distributions[] = [
                            'article_id' => $id,
                            'target_type' => 'division',
                            'target_id' => $divId,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }

                if ($departmentIds = $this->request->getPost('department_ids')) {
                    foreach ($departmentIds as $deptId) {
                        $distributions[] = [
                            'article_id' => $id,
                            'target_type' => 'department',
                            'target_id' => $deptId,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }

                if ($subDepartmentIds = $this->request->getPost('sub_department_ids')) {
                    foreach ($subDepartmentIds as $subDeptId) {
                        $distributions[] = [
                            'article_id' => $id,
                            'target_type' => 'sub_department',
                            'target_id' => $subDeptId,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }

                // Insert distribusi baru jika ada
                if (!empty($distributions)) {
                    $this->distributionModel->insertBatch($distributions);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admin/article')->with('success', 'Article updated successfully');
        } catch (\Exception $e) {
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
