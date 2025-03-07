<?php

namespace App\Controllers\Absensi;

use App\Controllers\BaseController;
use App\Models\Absensi\AbsensiModel;
use App\Models\Absensi\AbsenCategoryModel;
use App\Models\UserModel;
use App\Models\GroupUserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Absensi extends BaseController
{
    protected $absensiModel;
    protected $groupUserModel;
    protected $categoryModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->absensiModel = new AbsensiModel();
        $this->groupUserModel = new GroupUserModel();
        $this->categoryModel = new AbsenCategoryModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Attendance',
            'categories' => $this->categoryModel->findAll(),
        ];

        // Cek absensi pending terlebih dahulu
        $pendingAbsensi = $this->absensiModel->getPendingAbsensi(user_id());

        // Jika ada absensi pending, tampilkan itu
        if ($pendingAbsensi) {
            // Hitung total jam jika sudah ada jam masuk
            if ($pendingAbsensi['jam_masuk']) {
                // ... kode perhitungan total jam ...
            }
            $data['absensi'] = $pendingAbsensi;
            return view('absensi/index', $data);
        }

        // Jika tidak ada pending, baru cek absensi hari ini
        $absensi = $this->absensiModel->checkAbsenHariIni(user_id());

        // Cek jika ada request untuk absensi baru
        $newAttendance = session()->getFlashdata('new_attendance');

        if ($newAttendance && $absensi && $absensi['status'] == 'pulang') {
            return view('absensi/pre_absensi', $data);
        }

        if (!$absensi) {
            return view('absensi/pre_absensi', $data);
        }

        // Hitung total jam jika sudah tap out
        if ($absensi['jam_masuk'] && $absensi['jam_keluar']) {
            // Gabungkan tanggal dan jam masuk
            $masuk = strtotime($absensi['tanggal'] . ' ' . $absensi['jam_masuk']);

            // Gunakan tanggal_keluar jika ada, jika tidak gunakan tanggal absensi
            $tanggal_keluar = !empty($absensi['tanggal_keluar']) ? $absensi['tanggal_keluar'] : $absensi['tanggal'];
            $keluar = strtotime($tanggal_keluar . ' ' . $absensi['jam_keluar']);

            // Hitung selisih dalam menit
            $diffMinutes = ($keluar - $masuk) / 60;

            // Hitung jam dan menit
            $hours = floor($diffMinutes / 60);
            $minutes = $diffMinutes % 60;

            // Format total jam
            $absensi['total_jam'] = sprintf('%d jam %02d menit', $hours, $minutes);
        }

        $data['absensi'] = $absensi;
        return view('absensi/index', $data);
    }

    public function start_new_attendance()
    {
        if ($this->request->isAJAX()) {
            if (!logged_in()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You must login first'
                ]);
            }

            $absensi = $this->absensiModel->checkAbsenHariIni(user_id());

            // Jika status terakhir adalah pulang
            if ($absensi && $absensi['status'] == 'pulang') {
                // Set flash data untuk menandai request absensi baru
                session()->setFlashdata('new_attendance', true);

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'You can start new attendance now.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Cannot start new attendance. Please complete your current attendance first.'
                ]);
            }
        }
        return redirect()->back();
    }

    public function submitPreAbsensi()
    {
        if ($this->request->isAJAX()) {
            // Cek dulu apakah ada absensi pending
            $pendingAbsensi = $this->absensiModel->getPendingAbsensi(user_id());
            if ($pendingAbsensi) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You have pending attendance from ' . $pendingAbsensi['tanggal'] . '. Please complete it first.'
                ]);
            }

            $rules = [
                'category_id' => 'required',
                'judul_kegiatan' => 'required|min_length[5]'
            ];

            $canPreAttend = $this->absensiModel->canPreAttend(user_id());

            if (!$canPreAttend) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You have pre-attended today'
                ]);
            }

            $data = [
                'user_id' => user_id(),
                'tanggal' => date('Y-m-d'),
                'category_id' => $this->request->getPost('category_id'),
                'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
                'status' => 'pending'
            ];

            if ($this->absensiModel->save($data)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Pre-attendance is successful, please tap in'
                ]);
            }
        }
        return redirect()->back();
    }

    public function tapIn()
    {
        if ($this->request->isAJAX()) {
            $absensi = $this->absensiModel->checkAbsenHariIni(user_id());

            if (!$absensi) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You must fill in the category and activity title first'
                ]);
            }

            if ($absensi['status'] != 'pending') {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Anda sudah melakukan tap in hari ini'
                ]);
            }

            $data = [
                'id' => $absensi['id'],
                'jam_masuk' => date('H:i:s'),
                'status' => 'hadir'
            ];

            if ($this->absensiModel->save($data)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Tap in successfully'
                ]);
            }
        }
        return redirect()->back();
    }

    public function submitKegiatan()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();

            $rules = [
                'nik' => [
                    'rules' => 'required|regex_match[/^\d{8}$/]',
                    'errors' => [
                        'required' => 'NIK is required.',
                        'regex_match' => 'NIK must be exactly 8 digits.'
                    ]
                ],
                'pbr_tugas' => 'required|min_length[5]',
                'kegiatan_harian' => 'required|min_length[10]',
                'no_tiket' => [
                    'rules' => 'required|regex_match[/^\d{6}$/]',
                    'errors' => [
                        'required' => 'No tiket is required.',
                        'regex_match' => 'No tiket must be exactly 6 digits.'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $validation->getErrors()
                ]);
            }

            $absensi = $this->absensiModel->getPendingAbsensi(user_id());

            if (!$absensi || ($absensi['status'] != 'hadir' && $absensi['status'] != 'pending')) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You must tap in or have a pending attendance first'
                ]);
            }

            // Jika status masih pending, update status menjadi hadir
            $updateData = [
                'id' => $absensi['id'],
                'nik' => $this->request->getPost('nik'),
                'pbr_tugas' => $this->request->getPost('pbr_tugas'),
                'kegiatan_harian' => $this->request->getPost('kegiatan_harian'),
                'no_tiket' => $this->request->getPost('no_tiket'),
                'status' => 'hadir' // Update status ke hadir
            ];

            if ($this->absensiModel->save($updateData)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Activity saved successfully, now you can tap out'
                ]);
            }
        }
        return redirect()->back();
    }

    public function tapOut()
    {
        if ($this->request->isAJAX()) {
            // Get any pending attendance that needs tap out
            $absensi = $this->absensiModel->getPendingAbsensi(user_id());

            if (!$absensi || $absensi['status'] != 'hadir') {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You must tap in first'
                ]);
            }

            if (empty($absensi['kegiatan_harian']) || empty($absensi['no_tiket']) || empty($absensi['nik']) || empty($absensi['pbr_tugas'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'You must fill in the daily activities, ticket number, NIK, and assignor first'
                ]);
            }

            // If attendance is from a previous day, require using the correction modal
            if ($absensi['tanggal'] < date('Y-m-d')) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Please use the Correct Missed Tap Out feature for previous days'
                ]);
            }

            $data = [
                'id' => $absensi['id'],
                'jam_keluar' => date('H:i:s'),
                'tanggal_keluar' => date('Y-m-d'), // Tambahkan tanggal keluar
                'status' => 'pulang'
            ];

            if ($this->absensiModel->save($data)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Tap out successfully'
                ]);
            }
        }
        return redirect()->back();
    }

    public function correctTapOut()
    {
        if ($this->request->isAJAX()) {
            $absen_id = $this->request->getPost('absen_id');
            $jam_keluar = $this->request->getPost('jam_keluar');
            $tanggal_keluar = $this->request->getPost('tanggal_keluar');

            $absensi = $this->absensiModel->find($absen_id);

            if (!$absensi || $absensi['user_id'] != user_id()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid attendance record'
                ]);
            }

            // Hitung total jam
            $masuk = strtotime($absensi['tanggal'] . ' ' . $absensi['jam_masuk']);
            $keluar = strtotime($tanggal_keluar . ' ' . $jam_keluar);

            // Hitung selisih dalam menit
            $diffMinutes = ($keluar - $masuk) / 60;

            // Hitung jam dan menit
            $hours = floor($diffMinutes / 60);
            $minutes = $diffMinutes % 60;

            // Format total jam
            $total_jam = sprintf('%d jam %02d menit', $hours, $minutes);

            $data = [
                'id' => $absen_id,
                'jam_keluar' => $jam_keluar,
                'tanggal_keluar' => $tanggal_keluar,
                'total_jam' => $total_jam, // Tambahkan total jam ke data yang disimpan
                'status' => 'pulang'
            ];

            if ($this->absensiModel->save($data)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Tap out correction saved successfully',
                    'redirect' => true // Tambahkan flag redirect
                ]);
            }
        }
        return redirect()->back();
    }

    public function history()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $userGroup = $this->groupUserModel->where('user_id', user_id())->first();
        if ($userGroup['group_id'] != 1) {
            return redirect()->to('/absensi')->with('error', 'Akses ditolak!');
        }

        // Get selected month or default to current month
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');

        // Calculate start and end dates for the selected month
        $startDate = date('Y-m-01', strtotime($selectedMonth));
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        // Get attendance data with category filter
        $absensiData = $this->absensiModel->getHistory(user_id(), $startDate, $endDate, $selectedCategory);

        // Calculate total hours
        $totalHours = 0;
        foreach ($absensiData as &$item) {
            if ($item['jam_masuk'] && $item['jam_keluar']) {
                // Gunakan tanggal_keluar jika tersedia, jika tidak gunakan tanggal asli
                $tanggal_keluar = !empty($item['tanggal_keluar']) ? $item['tanggal_keluar'] : $item['tanggal'];

                // Gabungkan tanggal dan jam
                $masuk = strtotime($item['tanggal'] . ' ' . $item['jam_masuk']);
                $keluar = strtotime($tanggal_keluar . ' ' . $item['jam_keluar']);

                // Hitung selisih dalam menit
                $diffMinutes = ($keluar - $masuk) / 60;

                // Ubah menit ke format jam:menit
                $hours = floor($diffMinutes / 60);
                $minutes = $diffMinutes % 60;

                // Format string untuk ditampilkan, contoh: "2 jam 15 menit"
                $item['total_jam'] = sprintf('%d jam %02d menit', $hours, $minutes);

                // Tambahkan ke totalHours dalam format jam desimal
                $totalHours += $diffMinutes / 60;
            } else {
                $item['total_jam'] = '-';
            }
        }

        // Load categories for dropdown
        $categoryModel = new \App\Models\Absensi\AbsenCategoryModel();
        $categories = $categoryModel->findAll();

        $data = [
            'title' => 'Attendance History',
            'absensi' => $absensiData,
            'totalHours' => number_format($totalHours, 2),
            'categories' => $categories
        ];

        return view('absensi/history', $data);
    }

    public function preview()
    {
        if (!logged_in()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login first']);
        }

        // Get filter parameters
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category') ?? '';
        $startDate = date('Y-m-01', strtotime($selectedMonth));
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        // Get user details with joined tables
        $userModel = new \App\Models\UserModel();
        $userData = $userModel->select('users.*, divisions.name as division_name, departments.name as department_name, sub_departments.name as sub_department_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('divisions', 'divisions.id = users.division_id', 'left')
            ->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left')
            ->where('users.id', user_id())
            ->first();

        // Get attendance data with filter
        $absensiData = $this->absensiModel->getHistory(user_id(), $startDate, $endDate, $selectedCategory);

        // If no data found
        if (empty($absensiData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No attendance data found for the selected period and category'
            ]);
        }

        // Data untuk view
        $data = [
            'absensi' => $absensiData,
            'selectedMonth' => date('F Y', strtotime($selectedMonth)),
            'userData' => $userData,
            'selectedCategory' => $selectedCategory
        ];

        $data['logo_path'] = FCPATH . 'img/lintas.jpg';
        // Retrieve user signature
        $signaturePath = FCPATH . 'img/ttd/' . $userData['signature'];
        $data['signature_path'] = (file_exists($signaturePath) && $userData['signature']) ? $signaturePath : null;

        // Load mPDF
        $mpdf = new \Mpdf\Mpdf();

        // Render view to HTML
        $html = view('absensi/pdf', $data);

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Generate filename with category if selected
        $categorySegment = $selectedCategory ? '_Category_' . str_replace(' ', '_', $selectedCategory) : '';
        $filename = 'Laporan_Absensi_' . date('F_Y', strtotime($selectedMonth)) . $categorySegment . '.pdf';

        // Output PDF as string
        $pdfContent = $mpdf->Output('', 'S');

        // Return as JSON response with Base64 encoded PDF
        return $this->response->setJSON([
            'success' => true,
            'pdf' => base64_encode($pdfContent),
            'filename' => $filename
        ]);
    }
    public function superadminHistory()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');
        $selectedUser = $this->request->getGet('user');

        $startDate = date('Y-m-01', strtotime($selectedMonth));
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        $userModel = new UserModel();
        $users = $userModel->getAllhistoryUsers();

        // Get current user details for signature
        $userId = user_id();
        $userDetails = $userModel->select('users.*, users.signature, users.position as user_position, divisions.name as division_name, departments.name as department_name, sub_departments.name as sub_department_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('divisions', 'divisions.id = users.division_id', 'left')
            ->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left')
            ->where('users.id', $userId)
            ->first();

        $absensiData = $this->absensiModel->getSuperAdminHistory($startDate, $endDate, $selectedCategory, $selectedUser);

        // Calculate total hours
        $totalHours = 0;
        foreach ($absensiData as &$item) {
            if ($item['jam_masuk'] && $item['jam_keluar']) {
                $tanggal_keluar = !empty($item['tanggal_keluar']) ? $item['tanggal_keluar'] : $item['tanggal'];
                $masuk = strtotime($item['tanggal'] . ' ' . $item['jam_masuk']);
                $keluar = strtotime($tanggal_keluar . ' ' . $item['jam_keluar']);
                $diffMinutes = ($keluar - $masuk) / 60;
                $hours = floor($diffMinutes / 60);
                $minutes = $diffMinutes % 60;
                $item['total_jam'] = sprintf('%d jam %02d menit', $hours, $minutes);
                $totalHours += $diffMinutes / 60;
            } else {
                $item['total_jam'] = '-';
            }
        }

        $categoryModel = new \App\Models\Absensi\AbsenCategoryModel();
        $categories = $categoryModel->findAll();

        $data = [
            'title' => 'User Attendance List',
            'absensi' => $absensiData,
            'totalHours' => number_format($totalHours, 2),
            'categories' => $categories,
            'users' => $users,
            'selectedUser' => $selectedUser,
            'selectedMonth' => $selectedMonth,
            'userDetails' => $userDetails,
            'currentUser' => user()
        ];

        return view('absensi/superadmin/history', $data);
    }

    public function previewPdf()
    {
        // Validate AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            // Get and validate parameters
            $userId = $this->request->getGet('userId');
            $selectedMonth = $this->request->getGet('month');
            $isSigned = $this->request->getGet('signed') === 'true';

            if (!$userId || !$selectedMonth) {
                throw new \Exception('Missing required parameters');
            }

            // Get user details with proper error handling
            $userModel = new UserModel();
            $userDetails = $userModel->select('users.*, users.signature, users.position as user_position, divisions.name as division_name, departments.name as department_name, sub_departments.name as sub_department_name')
                ->join('departments', 'departments.id = users.department_id', 'left')
                ->join('divisions', 'divisions.id = users.division_id', 'left')
                ->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left')
                ->where('users.id', $userId)
                ->first();

            if (!$userDetails) {
                throw new \Exception('User not found');
            }

            // Get attendance data
            $startDate = date('Y-m-01', strtotime($selectedMonth));
            $endDate = date('Y-m-t', strtotime($selectedMonth));
            $absensi = $this->absensiModel->getSuperAdminHistory($startDate, $endDate, null, $userId);

            // Check if user has signature file
            if ($isSigned && isset($currentUser) && isset($currentUser->signature)) {
                $signaturePath = FCPATH . 'img/ttd/' . $currentUser->signature;
                if (!file_exists($signaturePath)) {
                    log_message('warning', 'Signature file not found: ' . $signaturePath);
                    // Don't throw exception, continue without signature
                }
            }

            // Check logo path exists
            $logo_path = FCPATH . 'img/lintas.jpg';
            if (!file_exists($logo_path)) {
                $logo_path = ''; // Set empty if file doesn't exist
                log_message('warning', 'Logo file not found: ' . $logo_path);
            }

            // Sort absensi by date consistently
            usort($absensi, function ($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });

            // Prepare data for PDF
            $data = [
                'userData' => $userDetails,
                'absensi' => $absensi,
                'selectedMonth' => $selectedMonth,
                'currentUser' => user(),
                'isSigned' => $isSigned,
                'logo_path' => $logo_path
            ];

            // Generate PDF with consistent settings
            $mpdf = new \Mpdf\Mpdf([
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            // Ensure proper content type and encoding
            $this->response->setHeader('Content-Type', 'application/json');

            // Generate PDF content
            $html = view('absensi/superadmin/pdf', $data);
            $mpdf->WriteHTML($html);
            $pdfContent = $mpdf->Output('', 'S');

            return $this->response->setJSON([
                'success' => true,
                'pdfData' => base64_encode($pdfContent)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Error generating PDF: ' . $e->getMessage()
                ]);
        }
    }

    public function signPdf()
    {
        // Validate AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            // Get JSON body data
            $jsonData = $this->request->getJSON(true);

            if (!isset($jsonData['userId']) || !isset($jsonData['month'])) {
                throw new \Exception('Missing required parameters');
            }

            $currentUser = user();
            if (!$currentUser) {
                throw new \Exception('User not found');
            }

            if (!$currentUser->signature) {
                throw new \Exception('Signature not found in user profile');
            }

            // Check if signature file exists
            $signaturePath = FCPATH . 'img/ttd/' . $currentUser->signature;
            if (!file_exists($signaturePath)) {
                throw new \Exception('Signature file not found: ' . $currentUser->signature);
            }

            // Update sign_pdf status to 1 for the selected month and user
            $userId = $jsonData['userId'];
            $month = $jsonData['month'];
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));

            // Use the model to update the database instead of direct DB access
            $db = \Config\Database::connect();
            $result = $db->table('absensi')
                ->where('user_id', $userId)
                ->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->set(['sign_pdf' => 1])
                ->update();

            if ($db->affectedRows() === 0) {
                log_message('warning', 'No records updated when signing PDF');
            }

            // Return success response with data needed for the frontend
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Document signed successfully',
                'userId' => $userId,
                'month' => $month
            ]);
        } catch (\Exception $e) {
            log_message('error', 'PDF Signing Error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Error signing document: ' . $e->getMessage()
                ]);
        }
    }
}
