<?php

namespace App\Controllers\Activity;

use App\Controllers\BaseController;
use App\Models\AdminActivityModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminActivity extends BaseController
{
    protected $activityModel;
    protected $db;

    public function __construct()
    {
        $this->activityModel = new AdminActivityModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $userId = user()->id;
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');

        $startDate = null;
        $endDate = null;
        if ($month) {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        // Ambil aktivitas
        $activities = $this->activityModel->getAdminActivities($userId, $startDate, $endDate);

        // Tambahkan total lembur untuk setiap aktivitas
        foreach ($activities as &$activity) {
            $activity['total_lembur'] = $this->calculateTotalLembur(
                $activity['activity_date'] . ' ' . $activity['start_time'],
                $activity['activity_date'] . ' ' . $activity['end_time']
            );
        }

        $data = [
            'title' => 'Overtime Activity Manual List',
            'activities' => $activities,
            'selectedMonth' => $month,
            'selectedYear' => $year
        ];

        return view('admin/activity/index', $data);
    }

    private function calculateTotalLembur($startTime, $endTime)
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);

        // Hitung selisih dalam menit
        $diffMinutes = ($end - $start) / 60;

        // Format hasil dalam jam dan menit
        return sprintf('%d jam %02d menit', floor($diffMinutes / 60), $diffMinutes % 60);
    }

    public function create()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        return view('admin/activity/create', ['title' => 'Create Activity']);
    }

    // Method untuk menyimpan data
    public function store()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Validasi input
        $rules = [
            'task' => 'required|min_length[3]',
            'location' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'activity_date' => 'required|valid_date',
            'description' => 'permit_empty',
            'nik' => 'required',
            'pbr_tugas' => 'required',
            'no_tiket' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Format data sebelum disimpan
        $data = [
            'user_id' => user()->id,
            'task' => trim($this->request->getPost('task')),
            'location' => trim($this->request->getPost('location')),
            'start_time' => date('H:i:s', strtotime($this->request->getPost('start_time'))),
            'end_time' => date('H:i:s', strtotime($this->request->getPost('end_time'))),
            'activity_date' => date('Y-m-d', strtotime($this->request->getPost('activity_date'))),
            'description' => trim($this->request->getPost('description')),
            'nik' => trim($this->request->getPost('nik')),
            'pbr_tugas' => trim($this->request->getPost('pbr_tugas')),
            'no_tiket' => trim($this->request->getPost('no_tiket')),
        ];

        try {
            // Use storeActivity() instead of insert()
            $saved = $this->activityModel->storeActivity($data);

            if ($saved) {
                return redirect()->to('/admin/activity')->with('success', 'Activity berhasil ditambahkan');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data');
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk menampilkan form edit
    public function edit($id = null)
    {
        if (!in_groups('admin')) {
            return redirect()->to('/');
        }

        $activity = $this->activityModel->getActivityById($id);

        if (empty($activity) || $activity['user_id'] != user_id()) {
            return redirect()->to('/admin/activity')->with('error', 'Activity not found');
        }

        return view('admin/activity/edit', [
            'activity' => $activity,
            'title' => 'Edit Activity'
        ]);
    }

    // Method untuk proses update
    public function update($id = null)
    {
        if (!in_groups('admin')) {
            return redirect()->to('/');
        }

        $activity = $this->activityModel->getActivityById($id);

        if (empty($activity) || $activity['user_id'] != user_id()) {
            return redirect()->to('/admin/activity')->with('error', 'Activity not found');
        }

        $rules = [
            'task' => 'required|min_length[3]',
            'location' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'activity_date' => 'required|valid_date',
            'description' => 'permit_empty',
            'nik' => 'required',
            'pbr_tugas' => 'required',
            'no_tiket' => 'required',
        ];

        if ($this->validate($rules)) {
            $updateData = [
                'task' => $this->request->getPost('task'),
                'location' => $this->request->getPost('location'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'activity_date' => $this->request->getPost('activity_date'),
                'description' => $this->request->getPost('description'),
                'nik' => $this->request->getPost('nik'),
                'pbr_tugas' => $this->request->getPost('pbr_tugas'),
                'no_tiket' => $this->request->getPost('no_tiket'),
            ];

            try {
                $updated = $this->activityModel->updateActivity($id, $updateData);
                if ($updated) {
                    return redirect()->to('/admin/activity')->with('success', 'Activity updated successfully');
                } else {
                    return redirect()->back()->withInput()->with('error', 'Failed to update activity');
                }
            } catch (\Exception $e) {
                log_message('error', 'Error updating activity: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'An error occurred while updating');
            }
        }

        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    public function delete($id = null)
    {
        if (!in_groups('admin')) {
            return redirect()->to('/');
        }

        try {
            $activity = $this->activityModel->getActivityById($id);

            if (!$activity || $activity['user_id'] != user_id()) {
                return redirect()->to('/admin/activity')->with('error', 'Activity not found');
            }

            if ($this->activityModel->deleteActivity($id)) {
                return redirect()->to('/admin/activity')->with('success', 'Activity deleted successfully');
            } else {
                return redirect()->to('/admin/activity')->with('error', 'Failed to delete activity');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting activity: ' . $e->getMessage());
            return redirect()->to('/admin/activity')->with('error', 'An error occurred while deleting');
        }
    }

    public function history()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Get current month and year if not provided
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $selectedUser = $this->request->getGet('user');

        $userModel = new UserModel();
        $users = $userModel->getAllhistoryUsers();

        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $selectedUser = $this->request->getGet('user');
        if ($selectedUser === "all") {
            $selectedUser = null;
        }

        // Ambil aktivitas
        $activities = $this->activityModel->getAllAdminActivities($startDate, $endDate, $selectedUser);

        // Tambahkan total lembur untuk setiap aktivitas
        foreach ($activities as &$activity) {
            $activity['total_lembur'] = $this->calculateTotalLembur(
                $activity['activity_date'] . ' ' . $activity['start_time'],
                $activity['activity_date'] . ' ' . $activity['end_time']
            );
        }

        $data = [
            'title' => 'Admin Activities History',
            'activities' => $activities,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'selectedUser' => $selectedUser,
            'users' => $users
        ];

        return view('admin/activity/history', $data);
    }
    // Add these methods to your AdminActivity controller

    public function export($type = 'excel')
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $userId = user()->id;
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year') ?? date('Y');

        // Calculate start and end dates for the filter
        $startDate = null;
        $endDate = null;
        if ($month) {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        // Get filtered activities
        $activities = $this->activityModel->getAdminActivities($userId, $startDate, $endDate);

        // Check if there's no data
        if (empty($activities)) {
            session()->setFlashdata('error', 'No data available for the selected period');
            return redirect()->back();
        }

        if ($type === 'excel') {
            return $this->exportExcel($activities, $month, $year);
        } else {
            return $this->exportPdf($activities, $month, $year);
        }
    }

    private function exportExcel($activities, $month, $year)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(user()->username)
            ->setLastModifiedBy(user()->username)
            ->setTitle('Activity Report')
            ->setSubject('Activity Report')
            ->setDescription('Activity Report generated on ' . date('Y-m-d H:i:s'));

        // Add header
        $period = $month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year : $year;
        $sheet->setCellValue('A1', 'PT. APLINUSA LINTASARTA');
        $sheet->setCellValue('A2', 'ACTIVITY REPORT');
        $sheet->setCellValue('A3', 'Period: ' . $period);
        $sheet->setCellValue('A4', 'Generated: ' . date('d F Y'));
        $sheet->setCellValue('A5', 'Name: ' . user()->username);

        // Merge cells for header
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');
        $sheet->mergeCells('A4:K4');
        $sheet->mergeCells('A5:K5');

        // Style the header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ];
        $sheet->getStyle('A1:K5')->applyFromArray($headerStyle);

        // Add table headers
        $headers = ['No', 'Date', 'NIK', 'Pemberi Tugas', 'No Ticket', 'Task', 'Description', 'Location', 'Start Time', 'End Time', 'Total Lembur'];

        $col = 'A';
        $row = 10;
        foreach ($headers as $header) {
            $sheet->setCellValue($col++ . $row, $header);
        }

        // Style the table headers
        $tableHeaderStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ];
        $sheet->getStyle('A7:K7')->applyFromArray($tableHeaderStyle);

        // Add data
        $row = 11;
        foreach ($activities as $i => $activity) {
            $total_lembur = $this->calculateTotalLembur(
                $activity['activity_date'] . ' ' . $activity['start_time'],
                $activity['activity_date'] . ' ' . $activity['end_time']
            );

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($activity['activity_date'])));
            $sheet->setCellValue('C' . $row, $activity['nik']);
            $sheet->setCellValue('D' . $row, $activity['pbr_tugas']);
            $sheet->setCellValue('E' . $row, $activity['no_tiket']);
            $sheet->setCellValue('F' . $row, $activity['task']);
            $sheet->setCellValue('G' . $row, $activity['description']);
            $sheet->setCellValue('H' . $row, $activity['location']);
            $sheet->setCellValue('I' . $row, date('H:i', strtotime($activity['start_time'])));
            $sheet->setCellValue('J' . $row, date('H:i', strtotime($activity['end_time'])));
            $sheet->setCellValue('K' . $row, $total_lembur);
            $row++;
        }

        // Auto-size untuk kolom baru
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'activity_report_' . ($month ? date('F_Y', mktime(0, 0, 0, $month, 1, $year)) : $year) . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    private function exportPdf($activities, $month, $year)
    {
        // Prepare the data for the view
        $period = $month ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : $year;

        // Get user details with joined tables
        $userModel = new \App\Models\UserModel();
        $userData = $userModel->select('users.*, divisions.name as division_name, departments.name as department_name, sub_departments.name as sub_department_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('divisions', 'divisions.id = users.division_id', 'left')
            ->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left')
            ->where('users.id', user_id())
            ->first();

        // Hitung total lembur
        $totalLemburMinutes = 0;
        foreach ($activities as $activity) {
            if (isset($activity['start_time']) && isset($activity['end_time'])) {
                $start = strtotime($activity['start_time']);
                $end = strtotime($activity['end_time']);
                $diffMinutes = ($end - $start) / 60;
                $totalLemburMinutes += $diffMinutes;
            }
        }

        // Format total lembur
        $totalLemburFormatted = sprintf(
            '%d jam %02d menit',
            floor($totalLemburMinutes / 60),
            $totalLemburMinutes % 60
        );

        $data = [
            'activities' => $activities,
            'selectedMonth' => $period,
            'totalActivities' => count($activities),
            'userData' => $userData,
            'totalLembur' => $totalLemburFormatted
        ];
        $data['logo_path'] = FCPATH . 'img/lintas.jpg';
        // Retrieve user signature
        $signaturePath = FCPATH . 'img/ttd/' . $userData['signature'];
        $data['signature_path'] = (file_exists($signaturePath) && $userData['signature']) ? $signaturePath : null;
        // Load mPDF with custom settings
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        // Load and write the PDF template
        $html = view('admin/activity/pdf_template', $data);
        $mpdf->WriteHTML($html);

        // Set filename with period information
        $filename = 'Activity_Report_' . ($month ? date('F_Y', mktime(0, 0, 0, $month, 1, $year)) : $year) . '.pdf';

        // Output PDF for download
        $mpdf->Output($filename, 'D');
        exit();
    }

    public function exportsuper($type = 'excel')
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year') ?? date('Y');
        $selectedUsers = $this->request->getGet('user');

        // Calculate start and end dates for the filter
        $startDate = null;
        $endDate = null;
        if ($month) {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        // Get filtered activities
        $selectedUsersArray = $selectedUsers === 'all' ? null : $selectedUsers;
        $activities = $this->activityModel->getAllAdminActivities($startDate, $endDate, $selectedUsersArray);
        // Tambahkan total lembur untuk setiap aktivitas
        foreach ($activities as &$activity) {
            $activity['total_lembur'] = $this->calculateTotalLembur(
                $activity['activity_date'] . ' ' . $activity['start_time'],
                $activity['activity_date'] . ' ' . $activity['end_time']
            );
        }
        // Handle empty results
        if (empty($activities)) {
            session()->setFlashdata('error', 'No data found for the selected filters.');
            return redirect()->back();
        }

        if ($type === 'excel') {
            return $this->exportHistoryExcel($activities, $month, $year);
        } else {
            return $this->exportHistoryPdf($activities, $month, $year);
        }
    }

    private function exportHistoryExcel($activities, $month, $year)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(user()->username)
            ->setLastModifiedBy(user()->username)
            ->setTitle('Activity History Report')
            ->setSubject('Activity History Report')
            ->setDescription('Activity History Report generated on ' . date('Y-m-d H:i:s'));

        // Add header
        $sheet->setCellValue('A1', 'PT. APLINUSA LINTASARTA');
        $sheet->setCellValue('A2', 'ACTIVITY HISTORY REPORT');
        $sheet->setCellValue('A3', 'Period: ' . ($month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year : $year));
        $sheet->setCellValue('A4', 'Generated: ' . date('d F Y'));
        $sheet->setCellValue('A5', 'Total Activities: ' . count($activities));

        // Merge cells for header
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');
        $sheet->mergeCells('A4:L4');
        $sheet->mergeCells('A5:L5');

        // Style the header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ];
        $sheet->getStyle('A1:L5')->applyFromArray($headerStyle);

        // Add table headers
        $headers = ['No', 'Date', 'User', 'nik', 'pbr_tugas', 'no_ticket', 'Task', 'Description', 'Location', 'Start Time', 'End Time', 'Total Lembur'];
        $col = 'A';
        $row = 11;
        foreach ($headers as $header) {
            $sheet->setCellValue($col++ . $row, $header);
        }

        // Style the table headers
        $tableHeaderStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ];
        $sheet->getStyle('A7:L7')->applyFromArray($tableHeaderStyle);

        // Add data or "No Data" message
        $row = 12;
        if (empty($activities)) {
            $sheet->mergeCells('A' . $row . ':L' . $row);
            $sheet->setCellValue('A' . $row, 'No activities found for the selected period and filters');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        } else {
            $i = 1;
            foreach ($activities as $activity) {
                $total_lembur = $this->calculateTotalLembur(
                    $activity['activity_date'] . ' ' . $activity['start_time'],
                    $activity['activity_date'] . ' ' . $activity['end_time']
                );

                $sheet->setCellValue('A' . $row, $i++);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($activity['activity_date'])));
                $sheet->setCellValue('C' . $row, $activity['username']);
                $sheet->setCellValue('D' . $row, $activity['nik']);
                $sheet->setCellValue('E' . $row, $activity['pbr_tugas']);
                $sheet->setCellValue('F' . $row, $activity['no_tiket']);
                $sheet->setCellValue('G' . $row, $activity['task']);
                $sheet->setCellValue('H' . $row, $activity['description']);
                $sheet->setCellValue('I' . $row, $activity['location']);
                $sheet->setCellValue('J' . $row, date('H:i', strtotime($activity['start_time'])));
                $sheet->setCellValue('K' . $row, date('H:i', strtotime($activity['end_time'])));
                $sheet->setCellValue('L' . $row, $total_lembur);

                $row++;
            }
        }
        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'activity_history_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    private function exportHistoryPdf($activities, $month, $year)
    {
        // Get user details with joined tables
        $userModel = new \App\Models\UserModel();
        $userData = $userModel->select('users.*, divisions.name as division_name, departments.name as department_name, sub_departments.name as sub_department_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('divisions', 'divisions.id = users.division_id', 'left')
            ->join('sub_departments', 'sub_departments.id = users.sub_department_id', 'left')
            ->where('users.id', user_id())
            ->first();
        // Hitung total lembur
        $totalLemburMinutes = 0;
        foreach ($activities as $activity) {
            if (isset($activity['start_time']) && isset($activity['end_time'])) {
                $start = strtotime($activity['start_time']);
                $end = strtotime($activity['end_time']);
                $diffMinutes = ($end - $start) / 60;
                $totalLemburMinutes += $diffMinutes;
            }
        }

        // Format total lembur
        $totalLemburFormatted = sprintf(
            '%d jam %02d menit',
            floor($totalLemburMinutes / 60),
            $totalLemburMinutes % 60
        );
        // Prepare the data for the view
        $data = [
            'activities' => $activities,
            'selectedMonth' => $month ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : $year,
            'totalActivities' => count($activities),
            'userData' => $userData,
            'generatedDate' => date('d F Y'),
            'totalLembur' => $totalLemburFormatted
        ];
        $data['logo_path'] = FCPATH . 'img/lintas.jpg';
        // Retrieve user signature
        $signaturePath = FCPATH . 'img/ttd/' . $userData['signature'];
        $data['signature_path'] = (file_exists($signaturePath) && $userData['signature']) ? $signaturePath : null;
        // Load mPDF
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        // Set document properties
        $mpdf->SetTitle('Activity History Report');
        $mpdf->SetAuthor(user()->username);
        $mpdf->SetCreator('PT. APLINUSA LINTASARTA');

        // Load the PDF view
        $html = view('admin/activity/history_pdf', $data);

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Set filename
        $filename = 'Activity_History_Report_' . ($month ? date('F_Y', mktime(0, 0, 0, $month, 1, $year)) : $year) . '_' . date('Y-m-d') . '.pdf';

        // Output PDF for download
        $mpdf->Output($filename, 'D');
        exit();
    }
    public function bulkUpload()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        return view('admin/activity/bulk_upload', ['title' => 'Bulk Upload Activities']);
    }

    public function processBulkUpload()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $validationRule = [
            'excel_file' => [
                'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls,csv]|max_size[excel_file,10240]',
                'errors' => [
                    'uploaded' => 'Please select a file to upload.',
                    'ext_in' => 'Only Excel files are allowed.',
                    'max_size' => 'File size must be less than 10MB.'
                ]
            ]
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $activities = [];
            $errors = [];

            // Start from row 2 to skip header
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                // Validate and map row data
                $activityData = $this->validateBulkUploadRow($rowData, $row);

                if ($activityData === false) {
                    continue; // Skip rows with validation errors
                }

                $activities[] = $activityData;
            }

            // Bulk insert using model method
            $activityModel = new AdminActivityModel();
            $result = $activityModel->bulkInsertActivities($activities);

            if ($result['success']) {
                return redirect()->to('/admin/activity')
                    ->with('success', sprintf(
                        'Successfully uploaded %d activities. %d rows skipped.',
                        $result['inserted'],
                        $result['skipped']
                    ));
            } else {
                return redirect()->back()->with('error', 'Failed to upload activities.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Bulk upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during file processing: ' . $e->getMessage());
        }
    }

    private function validateBulkUploadRow($rowData, $rowNumber)
    {
        // Ensure row has minimum required columns
        if (count($rowData) < 9) {
            log_message('warning', "Row $rowNumber skipped: Insufficient data columns");
            return false;
        }

        // Map columns: NIK, Pemberi Tugas, No Ticket, Date, Task, Location, Start Time, End Time, Description
        $mappedData = [
            'user_id' => user()->id,
            'nik' => trim($rowData[0] ?? ''),
            'pbr_tugas' => trim($rowData[1] ?? ''),
            'no_tiket' => trim($rowData[2] ?? ''),
            'activity_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData[3])->format('Y-m-d'),
            'task' => trim($rowData[4] ?? ''),
            'location' => trim($rowData[5] ?? ''),
            'start_time' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData[6])->format('H:i:s'),
            'end_time' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData[7])->format('H:i:s'),
            'description' => trim($rowData[8] ?? ''),
        ];

        // Validate mandatory fields
        $mandatoryFields = ['nik', 'pbr_tugas', 'no_tiket', 'activity_date', 'task', 'location', 'start_time', 'end_time'];

        foreach ($mandatoryFields as $field) {
            if (empty($mappedData[$field])) {
                log_message('warning', "Row $rowNumber skipped: Missing $field");
                return false;
            }
        }

        return $mappedData;
    }

    public function downloadTemplate()
    {
        return $this->response->download(FCPATH . 'img/lembur.xlsx', null);
    }
}
