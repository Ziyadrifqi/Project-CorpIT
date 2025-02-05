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

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->absensiModel = new AbsensiModel();
        $this->groupUserModel = new GroupUserModel();
        $this->categoryModel = new AbsenCategoryModel();
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


    public function exportExcel()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Cek apakah user memiliki akses
        $userGroup = $this->groupUserModel->where('user_id', user_id())->first();
        if ($userGroup['group_id'] != 1) {  // Sesuaikan dengan ID group admin
            return redirect()->to('/absensi')->with('error', 'Akses ditolak!');
        }

        // Get filter parameters
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');
        $startDate = date('Y-m-01', strtotime($selectedMonth));
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        // Ambil data absensi dengan filter
        $absensiData = $this->absensiModel->getHistory(user_id(), $startDate, $endDate, $selectedCategory);

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set page title
        $sheet->setTitle('Attendance Report');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(user()->username)
            ->setTitle('Attendance Report ' . date('F Y', strtotime($selectedMonth)))
            ->setSubject('Monthly Attendance')
            ->setDescription('Attendance report generated on ' . date('Y-m-d'));

        // Styling untuk header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Start Date');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Activity Title');
        $sheet->setCellValue('E1', 'Start Time');
        $sheet->setCellValue('F1', 'End Time');
        $sheet->setCellValue('G1', 'End Date');
        $sheet->setCellValue('H1', 'Total Hours');
        $sheet->setCellValue('I1', 'Assignor');
        $sheet->setCellValue('J1', 'Daily Activities');
        $sheet->setCellValue('K1', 'Ticket Number');

        // Apply header style
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(15);

        // Set data
        $row = 2;
        $totalHours = 0;
        foreach ($absensiData as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['category_name']);
            $sheet->setCellValue('D' . $row, $item['judul_kegiatan']);
            $sheet->setCellValue('E' . $row, $item['jam_masuk'] ? date('H:i', strtotime($item['jam_masuk'])) : '-');
            $sheet->setCellValue('F' . $row, $item['jam_keluar'] ? date('H:i', strtotime($item['jam_keluar'])) : '-');
            $sheet->setCellValue('G' . $row, $item['tanggal_keluar'] ? date('d/m/Y', strtotime($item['tanggal_keluar'])) : '-');

            // Calculate total hours
            if ($item['jam_masuk'] && $item['jam_keluar']) {
                $tanggal_keluar = !empty($item['tanggal_keluar']) ? $item['tanggal_keluar'] : $item['tanggal'];
                $masuk = strtotime($item['tanggal'] . ' ' . $item['jam_masuk']);
                $keluar = strtotime($tanggal_keluar . ' ' . $item['jam_keluar']);
                $diffMinutes = ($keluar - $masuk) / 60;
                $hours = floor($diffMinutes / 60);
                $minutes = $diffMinutes % 60;
                $totalHoursItem = sprintf('%d jam %02d menit', $hours, $minutes);
                $totalHoursDecimal = $diffMinutes / 60;
            } else {
                $totalHoursItem = '-';
                $totalHoursDecimal = 0;
            }

            $sheet->setCellValue('H' . $row, $totalHoursItem);
            $sheet->setCellValue('I' . $row, $item['pbr_tugas'] ?? '-');
            $sheet->setCellValue('J' . $row, $item['kegiatan_harian'] ?? '-');
            $sheet->setCellValue('K' . $row, $item['no_tiket'] ?? '-');

            // Style for data rows
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            $totalHours += $totalHoursDecimal;
            $row++;
        }

        // Add total hours summary
        $sheet->setCellValue('G' . $row, 'Total Hours:');
        $sheet->setCellValue('H' . $row, sprintf('%.2f jam', $totalHours));
        $sheet->getStyle('G' . $row . ':H' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6E6']
            ]
        ]);

        // Filename with month and year
        $filename = 'Attendance_Report_' . date('F_Y', strtotime($selectedMonth)) . '_' . date('Y-m-d') . '.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportPdf()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Get filter parameters
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');
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

        // Data untuk view
        $data = [
            'absensi' => $absensiData,
            'selectedMonth' => date('F Y', strtotime($selectedMonth)),
            'userData' => $userData
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

        // Output PDF
        $filename = 'Laporan_Absensi_' . date('F_Y', strtotime($selectedMonth)) . '_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }

    public function superadminHistory()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Get selected month or default to current month
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');
        $selectedUser = $this->request->getGet('user');

        // Calculate start and end dates for the selected month
        $startDate = date('Y-m-01', strtotime($selectedMonth));
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        // Get all users for filter dropdown
        $userModel = new UserModel();
        $users = $userModel->getAllhistoryUsers();

        // Get attendance data with category and user filter
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

        // Load categories for dropdown
        $categoryModel = new \App\Models\Absensi\AbsenCategoryModel();
        $categories = $categoryModel->findAll();

        $data = [
            'title' => 'User Attendance List',
            'absensi' => $absensiData,
            'totalHours' => number_format($totalHours, 2),
            'categories' => $categories,
            'users' => $users,
            'selectedUser' => $selectedUser
        ];

        return view('absensi/superadmin/history', $data);
    }

    public function superadminExportExcel()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Get filter parameters
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');
        $selectedUser = $this->request->getGet('user');
        $startDate = date('Y-m-01', strtotime($selectedMonth));
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        // Ambil data absensi dengan filter
        $absensiData = $this->absensiModel->getSuperAdminHistory($startDate, $endDate, $selectedCategory, $selectedUser);

        if (empty($absensiData)) {
            session()->setFlashdata('error', 'No data available for the selected period');
            return redirect()->back();
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set page title
        $sheet->setTitle('User Attendance Report');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(user()->username)
            ->setTitle('User Attendance Report ' . date('F Y', strtotime($selectedMonth)))
            ->setSubject('Monthly User Attendance')
            ->setDescription('User attendance report generated on ' . date('Y-m-d'));

        // Styling untuk header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'User');
        $sheet->setCellValue('C1', 'Start Date');
        $sheet->setCellValue('D1', 'Category');
        $sheet->setCellValue('E1', 'Activity Title');
        $sheet->setCellValue('F1', 'Start Time');
        $sheet->setCellValue('G1', 'End Time');
        $sheet->setCellValue('H1', 'End Date');
        $sheet->setCellValue('I1', 'Total Hours');
        $sheet->setCellValue('J1', 'Daily Activities');
        $sheet->setCellValue('K1', 'Ticket Number');

        // Apply header style
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(15);

        // Set data
        $row = 2;
        $totalHours = 0;
        foreach ($absensiData as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['user_name']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('D' . $row, $item['category_name']);
            $sheet->setCellValue('E' . $row, $item['judul_kegiatan']);
            $sheet->setCellValue('F' . $row, $item['jam_masuk'] ? date('H:i', strtotime($item['jam_masuk'])) : '-');
            $sheet->setCellValue('G' . $row, $item['jam_keluar'] ? date('H:i', strtotime($item['jam_keluar'])) : '-');
            $sheet->setCellValue('H' . $row, $item['tanggal_keluar'] ? date('d/m/Y', strtotime($item['tanggal_keluar'])) : '-');

            // Calculate total hours
            if ($item['jam_masuk'] && $item['jam_keluar']) {
                $tanggal_keluar = !empty($item['tanggal_keluar']) ? $item['tanggal_keluar'] : $item['tanggal'];
                $masuk = strtotime($item['tanggal'] . ' ' . $item['jam_masuk']);
                $keluar = strtotime($tanggal_keluar . ' ' . $item['jam_keluar']);
                $diffMinutes = ($keluar - $masuk) / 60;
                $hours = floor($diffMinutes / 60);
                $minutes = $diffMinutes % 60;
                $totalHoursItem = sprintf('%d jam %02d menit', $hours, $minutes);
                $totalHoursDecimal = $diffMinutes / 60;
            } else {
                $totalHoursItem = '-';
                $totalHoursDecimal = 0;
            }

            $sheet->setCellValue('I' . $row, $totalHoursItem);
            $sheet->setCellValue('J' . $row, $item['kegiatan_harian'] ?? '-');
            $sheet->setCellValue('K' . $row, $item['no_tiket'] ?? '-');

            // Style for data rows
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            $totalHours += $totalHoursDecimal;
            $row++;
        }

        // Add total hours summary
        $sheet->setCellValue('H' . $row, 'Total Hours:');
        $sheet->setCellValue('I' . $row, sprintf('%.2f jam', $totalHours));
        $sheet->getStyle('H' . $row . ':I' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6E6']
            ]
        ]);

        // Filename with month and year
        $filename = 'User_Attendance_Report_' . date('F_Y', strtotime($selectedMonth)) . '_' . date('Y-m-d') . '.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportPdfsuper()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Get filter parameters
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedCategory = $this->request->getGet('category');
        $selectedUser = $this->request->getGet('user');
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
        // Ambil data absensi dengan filter
        $absensiData = $this->absensiModel->getSuperAdminHistory($startDate, $endDate, $selectedCategory, $selectedUser);

        // Handle empty results
        if (empty($absensiData)) {
            session()->setFlashdata('error', 'No data available for the selected period');
            return redirect()->back();
        }
        // Data untuk view
        $data = [
            'absensi' => $absensiData,
            'selectedMonth' => date('F Y', strtotime($selectedMonth)),
            'userData' => $userData
        ];

        // Load mPDF
        $mpdf = new \Mpdf\Mpdf();

        // Render view to HTML
        $html = view('absensi/superadmin/pdf', $data);

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Output PDF
        $filename = 'Laporan_Absensi_' . date('F_Y', strtotime($selectedMonth)) . '_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }
}
