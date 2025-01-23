<?php

namespace App\Controllers;

use App\Models\FileUploadModel;
use App\Models\CategoryPermissionModel;
use CodeIgniter\Controller;
use App\Models\FileuploadDistributionModel;

class FileUploadController extends Controller
{
    protected $fileUploadModel;
    protected $distributionModel;
    protected $categoryPermissionModel;
    protected $db;

    public function __construct()
    {
        $this->fileUploadModel = new FileUploadModel();
        $this->categoryPermissionModel = new CategoryPermissionModel();
        $this->distributionModel = new FileuploadDistributionModel();
        $this->db = \Config\Database::connect();
    }

    // Display list of files
    public function index()
    {
        $data = [
            'title' => 'Work Instruction List',
            'files' => $this->fileUploadModel->getAllFileWithDistributions()
        ];

        return view('admin/fileupload/index', $data);
        // Pengguna biasa hanya melihat artikel mereka berdasarkan hierarki
        $data = [
            'title' => 'Work Instructions',
            'files' => $this->fileUploadModel->getFileForUser($userData)
        ];
        return view('pages/article/index', $data);
    }

    // Display create file upload form
    public function create()
    {
        // Dapatkan user_id dari session
        $userId = user_id();

        // Dapatkan kategori yang diizinkan untuk user
        $allowedCategories = $this->categoryPermissionModel->getCategoriesForUsers($userId);

        $data = array_merge([
            'title' => 'Add New Work Instruction',
            'categories' => $allowedCategories // Menggunakan kategori yang diizinkan
        ], $this->getHierarchyData());

        return view('admin/fileupload/create', $data);
    }

    // Store new file upload
    public function store()
    {
        $userId = user_id();
        $allowedCategoryIds = $this->categoryPermissionModel->getCategoriesForUser($userId);

        // Ambil kategori yang dipilih sebagai array
        $selectedCategoryIds = $this->request->getPost('category_id');

        // Validasi apakah semua kategori yang dipilih diizinkan
        foreach ($selectedCategoryIds as $categoryId) {
            if (!in_array($categoryId, $allowedCategoryIds)) {
                return redirect()->back()->withInput()
                    ->with('error', 'You do not have permission to use this category.');
            }
        }

        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'author' => 'required',
            'userfile' => 'uploaded[userfile]|max_size[userfile,15360]',

        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('userfile');
        $fileName = $file->getRandomName();
        $now = date('Y-m-d H:i:s');
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'author' => $this->request->getPost('author'),
            'status' => $this->request->getPost('status', 'draft'),
            'file_path' => 'public/fileupload/' . $fileName,
            'created_at' => $now,
            'updated_at' => $now
        ];

        // Start database transaction
        $this->db->transStart();

        // Pindahkan file
        $file->move('public/fileupload', $fileName);

        // Masukkan data file ke dalam database
        $fileId = $this->fileUploadModel->insert($data);

        // Simpan kategori yang dipilih ke dalam tabel file_categories
        foreach ($selectedCategoryIds as $categoryId) {
            $this->db->table('file_categories')->insert([
                'fileuploads_id' => $fileId,
                'category_id' => $categoryId
            ]);
        }

        $this->db->transComplete();

        // Proses distribusi hierarkis
        $distributions = $this->processHierarchicalDistributions();

        if (!$this->distributionModel->saveDistributions($fileId, $distributions)) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Failed to save distributions');
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to save File');
        }
        return redirect()->to('/admin/fileuploads')->with('success', 'File created successfully');
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
    // Edit an existing file
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('/admin/fileuploads')
                ->with('error', 'ID tidak ditemukan');
        }

        $userId = user_id();
        $allowedCategories = $this->categoryPermissionModel->getCategoriesForUsers($userId);

        $file = $this->fileUploadModel->getFileWithDistributions($id);

        if (!$file) {
            return redirect()->to('/admin/fileuploads')
                ->with('error', 'File tidak ditemukan');
        }

        $data = array_merge([
            'title' => 'Edit Work Instruction',
            'file' => $file,
            'validation' => \Config\Services::validation(),
            'categories' => $allowedCategories
        ], $this->getHierarchyData());

        return view('admin/fileupload/edit', $data);
    }

    // Memproses update data
    public function update($id = null)
    {
        if ($id === null) {
            return redirect()->to('/admin/fileuploads')
                ->with('error', 'ID tidak ditemukan');
        }

        $userId = user_id();
        $allowedCategoryIds = $this->categoryPermissionModel->getCategoriesForUser($userId);

        // Ambil kategori yang dipilih sebagai array
        $selectedCategoryIds = $this->request->getPost('category_id');

        // Validasi apakah semua kategori yang dipilih diizinkan
        foreach ($selectedCategoryIds as $categoryId) {
            if (!in_array($categoryId, $allowedCategoryIds)) {
                return redirect()->back()->withInput()
                    ->with('error', 'You do not have permission to use this category.');
            }
        }

        // Ambil data file yang ada
        $file = $this->fileUploadModel->find($id);
        if (!$file) {
            return redirect()->to('/admin/fileuploads')
                ->with('error', 'File tidak ditemukan');
        }

        // Set rules validasi
        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'author' => 'required',
            'updated_at' => 'required|valid_date[Y-m-d]'
        ];

        // Tambah rule untuk file jika ada file baru diupload
        $newFile = $this->request->getFile('userfile');
        if ($newFile->isValid()) {
            $rules['userfile'] = 'uploaded[userfile]|max_size[userfile,15360]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $updated_at = $this->request->getPost('updated_at')
            ? date('Y-m-d H:i:s', strtotime($this->request->getPost('updated_at')))
            : date('Y-m-d H:i:s');

        // Siapkan data untuk update
        $updateData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'author' => $this->request->getPost('author'),
            'status' => $this->request->getPost('status'),
            'updated_at' => $updated_at
        ];

        // Mulai transaksi
        $this->db->transStart();

        try {
            // Handle file upload jika ada file baru
            if ($newFile->isValid() && !$newFile->hasMoved()) {
                $fileName = $newFile->getRandomName();
                $uploadPath = FCPATH . 'public/fileupload/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Hapus file lama
                $oldFilePath = FCPATH . $file['file_path'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Upload file baru
                $newFile->move($uploadPath, $fileName);
                $updateData['file_path'] = 'public/fileupload/' . $fileName;
            }

            // Proses distribusi
            $distributions = $this->processHierarchicalDistributions();
            $this->distributionModel->updateDistributions($id, $distributions);

            // Update database
            $this->fileUploadModel->update($id, $updateData);

            // Hapus kategori lama
            $this->db->table('file_categories')->where('fileuploads_id', $id)->delete();

            // Simpan kategori baru
            foreach ($selectedCategoryIds as $categoryId) {
                $this->db->table('file_categories')->insert([
                    'fileuploads_id' => $id,
                    'category_id' => $categoryId
                ]);
            }

            $this->db->transComplete();
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate file: ' . $e->getMessage());
        }

        if ($this->db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate file');
        }

        // Redirect ke halaman file uploads dengan pesan sukses
        return redirect()->to('/admin/fileuploads')
            ->with('success', 'File berhasil diperbarui');
    }

    public function delete($id = null)
    {
        if ($id === null) {
            return redirect()->to('/admin/fileuploads')->with('error', 'ID tidak ditemukan.');
        }

        // Mulai transaksi
        $this->db->transStart();

        try {
            // Temukan file berdasarkan ID
            $file = $this->fileUploadModel->find($id);

            if ($file) {
                // Hapus distribusi terkait
                $this->distributionModel->deleteByFileId($id);

                // Hapus kategori terkait
                $this->db->table('file_categories')->where('fileuploads_id', $id)->delete();

                // Hapus file fisik jika ada
                $filePath = FCPATH . $file['file_path'];
                log_message('info', 'Attempting to delete file at: ' . $filePath);

                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        throw new \Exception('Gagal menghapus file: ' . $filePath);
                    }
                } else {
                    log_message('warning', 'File tidak ditemukan: ' . $filePath);
                }

                // Hapus record file dari database
                $this->fileUploadModel->delete($id);
            } else {
                return redirect()->to('/admin/fileuploads')->with('error', 'File tidak ditemukan.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->to('/admin/fileuploads')->with('error', 'Gagal menghapus file.');
            }

            return redirect()->to('/admin/fileuploads')->with('success', 'File berhasil dihapus.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/admin/fileuploads')->with('error', 'Terjadi kesalahan saat menghapus file: ' . $e->getMessage());
        }
    }
}
