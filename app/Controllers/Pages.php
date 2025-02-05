<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\FileUploadModel;
use App\Models\CategoryModel;
use App\Models\MonitoringTicketModel;

class Pages extends BaseController
{
    protected $db;
    protected $articleModel;
    protected $fileUploadModel;
    protected $categoryModel;
    protected $monitoringTicketModel;

    public function __construct()
    {
        // Load the database and article model
        $this->db = \Config\Database::connect();
        $this->articleModel = new ArticleModel();
        $this->fileUploadModel = new FileUploadModel();
        $this->categoryModel = new CategoryModel();
        $this->monitoringTicketModel = new MonitoringTicketModel();
    }

    public function index()
    {
        helper('text');

        // Get the logged-in user's ID
        $userId = user()->id ?? null; // Use null if not logged in

        // Get user groups if logged in
        $userGroups = $userId ? $this->getUserGroups($userId) : [];

        // Fetch articles based on user groups or show public articles if not logged in
        $articles = $this->articleModel->getArticlesForUser($userGroups);

        // Fetch categories
        $categories = $this->categoryModel->findAll();

        // Prepare data for the view
        $data = [
            'title' => 'Home',
            'articles' => $articles,
            'categories' => $categories,
            'recent_posts' => $this->getRecentPosts()
        ];

        // Load the appropriate view based on user group
        if ($userId) {
            $groupId = $this->getUserGroupId($userId);
            if ($groupId == 2) {
                return view('template/index', $data);
            } else {
                return view('user/index', $data);
            }
        } else {
            // If not logged in, just show the public view
            return view('template/index', $data);
        }
    }

    private function getUserGroupId($userId)
    {
        $group = $this->db->table('auth_groups_users')
            ->where('user_id', $userId)
            ->get()
            ->getFirstRow();

        return $group ? $group->group_id : null;
    }

    // Method to get user groups
    private function getUserGroups($userId)
    {
        // Ambil data grup pengguna langsung dari tabel `users`
        return $this->db->table('users')
            ->select('directorate_id, division_id, department_id, sub_department_id')
            ->where('id', $userId)
            ->get()
            ->getRowArray();
    }

    public function storeTicket()
    {
        // Validasi input
        $this->validate([
            'ticket_number' => 'required|is_unique[monitoring_tickets.ticket_number]',
            'subject' => 'required',
            'status_ticket' => 'required',
            'status_approval' => 'required',
            'technician' => 'permit_empty',
            'resolution' => 'permit_empty',
            'conversation' => 'permit_empty',
        ]);

        // Jika ada error validasi, kembalikan dengan pesan error
        if ($this->validator->getErrors()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Dapatkan user_id dari session
        $userId = user()->id;

        // Persiapkan data untuk disimpan
        $data = [
            'ticket_number' => $this->request->getPost('ticket_number'),
            'subject' => $this->request->getPost('subject'),
            'technician' => $this->request->getPost('technician'),
            'status_ticket' => $this->request->getPost('status_ticket'),
            'status_approval' => $this->request->getPost('status_approval'),
            'resolution' => $this->request->getPost('resolution'),
            'conversation' => $this->request->getPost('conversation'),
            'user_id' => $userId // Simpan user ID yang membuat tiket
        ];

        // Cek apakah ticket_number sudah ada (duplikat)
        $existingTicket = $this->monitoringTicketModel->where('ticket_number', $data['ticket_number'])->first();

        if ($existingTicket) {
            return redirect()->back()->with('error', 'Ticket number sudah digunakan. Silakan coba dengan nomor tiket yang berbeda.');
        }

        // Simpan data tiket
        if ($this->monitoringTicketModel->save($data)) {
            return redirect()->to('/pages/createTicket')->with('message', 'Ticket berhasil dibuat!');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat tiket.');
        }
    }

    public function createTicket()
    {
        helper('text');
        // Ambil user_id dari session
        $userId = user()->id;

        // Ambil riwayat tiket untuk pengguna yang sedang login
        $ticketModel = new \App\Models\MonitoringTicketModel();
        $ticketHistory = $ticketModel->getTicketHistory($userId);

        // Siapkan data untuk view
        $data = [
            'title' => 'Create Ticket',
            'ticketHistory' => $ticketHistory,
            'recent_posts' => $this->getRecentPosts()
        ];

        // Load the create_ticket view
        return view('template/create_ticket', $data);
    }
    public function monitoring_ticket($ticketNumber = null)
    {
        helper('text');
        $data = [
            'title' => 'Monitoring Ticket',
            'prefilledTicketNumber' => $ticketNumber,
            'recent_posts' => $this->getRecentPosts()
        ];

        return view('template/monitoring_ticket', $data);
    }

    public function monitoringTicket()
    {
        helper('text');
        $data = [
            'title' => 'Monitoring Ticket',
            'recent_posts' => $this->getRecentPosts()
        ];

        return view('template/monitoring_ticket', $data);
    }

    public function searchTicket()
    {
        $ticketNumber = $this->request->getPost('ticket_number');
        $monitoringTicketModel = new MonitoringTicketModel();

        $ticket = $monitoringTicketModel->searchTicketByNumber($ticketNumber);

        if ($ticket) {
            return $this->response->setJSON([
                'status' => 'success',
                'ticket' => [
                    'ticket_number' => $ticket['ticket_number'],
                    'subject' => $ticket['subject'],
                    'technician' => $ticket['technician_name'],
                    'status_ticket' => $ticket['status_ticket'],
                    'status_approval' => $ticket['status_approval'],
                    'resolution' => $ticket['resolution'],
                    'conversation' => $ticket['conversation']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ticket not found'
            ]);
        }
    }
    public function publication()
    {
        helper('text');

        $userId = user()->id ?? null;
        $userGroups = $this->getUserGroups($userId);
        $files = $this->fileUploadModel->getFileForUser($userGroups);
        $categories = $this->categoryModel->findAll();

        $filesByCategory = [];
        foreach ($categories as $category) {
            $filesByCategory[$category['id']] = array_filter($files, function ($file) use ($category) {
                // Pecah category_id menjadi array
                $categoryIds = explode(',', $file['category_id']);
                return in_array($category['id'], $categoryIds) && $file['status'] === 'published';
            });

            // Add full path to each file
            foreach ($filesByCategory[$category['id']] as &$file) {
                $file['full_path'] = base_url('public/fileupload/' . $file['file_path']);
            }
        }

        $data = [
            'title' => 'Work Instruction',
            'categories' => $categories,
            'filesByCategory' => $filesByCategory,
            'recent_posts' => $this->getRecentPosts()
        ];

        return view('template/publication', $data);
    }

    public function viewFile($id)
    {
        $userId = user()->id ?? null;
        $userGroups = $this->getUserGroups($userId);
        $file = $this->fileUploadModel->getFileWithDistributions($id);

        if (empty($file)) {
            return redirect()->to('/pages/index')->with('error', 'File tidak ditemukan.');
        }

        $userFiles = $this->fileUploadModel->getFileForUser($userGroups);
        $hasAccess = array_filter($userFiles, fn($userFile) => $userFile['id'] == $id);

        if (!$hasAccess) {
            return redirect()->to('/pages/index')->with('error', 'Anda tidak memiliki akses ke file ini.');
        }

        $filePath = FCPATH . 'public/fileupload/' . $file['file_path'];

        if (!file_exists($filePath)) {
            return redirect()->to('/pages/index')->with('error', 'File tidak ditemukan di server.');
        }

        return $this->response->download($filePath, null)->setFileName($file['title']);
    }


    public function article()
    {
        helper('text');
        $userId = user()->id ?? null;
        $userGroups = $userId ? $this->getUserGroups($userId) : null;

        // Pagination configuration
        $perPage = 6; // Number of articles per page
        $page = $this->request->getVar('page') ?? 1;

        // Get articles accessible to the user (or public articles for non-logged in users)
        $articles = $this->articleModel->getArticlesForUser($userGroups);
        $categories = $this->categoryModel->findAll();

        // Pagination logic for articles by category
        $articlesByCategory = [];
        foreach ($categories as $category) {
            // Filter articles that belong to the current category
            $categoryArticles = array_filter($articles, function ($arti) use ($category) {
                // Check if the category ID is in the comma-separated list of category IDs
                return in_array($category['id'], explode(',', $arti['category_ids'])) && $arti['status'] === 'published';
            });

            // Paginate articles for each category
            $paginatedArticles = array_slice($categoryArticles, ($page - 1) * $perPage, $perPage);

            $articlesByCategory[$category['id']] = $paginatedArticles;
        }

        // Calculate total pages for each category
        $totalPagesByCategory = [];
        foreach ($categories as $category) {
            $categoryArticles = array_filter($articles, function ($arti) use ($category) {
                // Check if the category ID is in the comma-separated list of category IDs
                return in_array($category['id'], explode(',', $arti['category_ids'])) && $arti['status'] === 'published';
            });
            $totalPagesByCategory[$category['id']] = ceil(count($categoryArticles) / $perPage);
        }

        $data = [
            'title' => 'Articles',
            'categories' => $categories,
            'articlesByCategory' => $articlesByCategory,
            'totalPagesByCategory' => $totalPagesByCategory,
            'currentPage' => $page,
            'recent_posts' => $this->getRecentPosts()
        ];

        return view('template/news', $data);
    }

    public function view($id)
    {
        helper('text');
        $userId = user()->id ?? null;
        $userGroups = $userId ? $this->getUserGroups($userId) : null;

        // Get article with distributions
        $article = $this->articleModel->getArticleWithDistributions($id);

        if (empty($article)) {
            return redirect()->to('/pages/index')->with('error', 'Artikel tidak ditemukan.');
        }

        // Check if article is public or user has access
        if ($article['type'] !== 'public' && !$userId) {
            return redirect()->to('/login')->with('error', 'Please login to view this article.');
        }

        // Get articles accessible to the user
        $articles = $this->articleModel->getArticlesForUser($userGroups);

        // Get categories with article count
        $categories = $this->categoryModel->findAll();
        foreach ($categories as &$category) {
            // Count published articles in each category that the user can access
            $category['article_count'] = count(array_filter($articles, function ($art) use ($category) {
                return in_array($category['id'], explode(',', $art['category_ids'])) && $art['status'] === 'published';
            }));
        }

        // Prepare recent posts
        $recentPosts = [];
        foreach ($articles as $art) {
            if ($art['status'] === 'published') {
                $recentPosts[] = $art;
            }
        }

        // Sort and limit recent posts
        usort($recentPosts, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        $recentPosts = array_slice($recentPosts, 0, 3); // Get the latest 3 posts

        $data = [
            'title' => $article['title'],
            'article' => $article,
            'categories' => $categories,
            'recent_posts' => $recentPosts,
        ];

        return view('template/news-details', $data);
    }
    public function getRecentPosts($limit = 3)
    {
        $userId = user()->id ?? null;
        $userGroups = $userId ? $this->getUserGroups($userId) : null;

        // Get articles accessible to the user or public articles for non-logged in users
        $articles = $this->articleModel->getArticlesForUser($userGroups);

        $recentPosts = [];
        foreach ($articles as $article) {
            if ($article['status'] === 'published') {
                $recentPosts[] = $article;
            }
        }

        // Sort and limit recent posts
        usort($recentPosts, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($recentPosts, 0, $limit);
    }
    public function profile()
    {
        helper('text');
        $userId = user()->id; // Get the currently logged-in user's ID

        // Query to get user details along with sub-department, department, division, and directorate names
        $query = $this->db->table('users')
            ->select('users.username, users.fullname, users.email, users.user_image, users.position')
            ->select('sub_departments.name as sub_department_name, departments.name as department_name, divisions.name as division_name, directorates.name as directorate_name')
            ->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('divisions', 'divisions.id = users.division_id', 'left')
            ->join('directorates', 'directorates.id = users.directorate_id', 'left')
            ->where('users.id', $userId)
            ->get()
            ->getRow();

        $data = [
            'title' => 'My Profile',
            'user' => $query, // Pass the query result to the view
            'recent_posts' => $this->getRecentPosts()
        ];

        return view('template/profile', $data);
    }

    public function updateProfile()
    {
        $userId = user()->id;

        // Get the current user data
        $user = $this->db->table('users')->getWhere(['id' => $userId])->getRow();

        $data = [
            'username' => $this->request->getPost('username'),
            'fullname' => $this->request->getPost('fullname'),
            'email' => $this->request->getPost('email'),
        ];

        // Check if a file image has been uploaded
        if ($this->request->getFile('user_image')->isValid()) {
            $file = $this->request->getFile('user_image');

            // Move the file to the img folder
            $fileName = $file->getRandomName(); // Generate a random file name
            $file->move('img', $fileName); // Move the file to the img folder

            // Delete old image if it exists
            if ($user->user_image && $user->user_image !== 'default.png') {
                // Remove the old file from the server
                if (file_exists('img/' . $user->user_image)) {
                    unlink('img/' . $user->user_image); // Delete the old file
                }
            }

            // Save the new image file name in the database
            $data['user_image'] = $fileName;
        }

        // Update user data in the database
        $this->db->table('users')->update($data, ['id' => $userId]);
        return redirect()->to('/pages/profile')->with('success', 'Profile updated successfully');
    }
}
