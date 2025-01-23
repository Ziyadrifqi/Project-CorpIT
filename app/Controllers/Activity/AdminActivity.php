<?php

namespace App\Controllers\Activity;

use App\Controllers\BaseController;
use App\Models\AdminActivityModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year') ?? date('Y');

        // Calculate start and end dates for the filter
        $startDate = null;
        $endDate = null;
        if ($month) {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        $data = [
            'title' => 'Activity List',
            'activities' => $this->activityModel->getAdminActivities($userId, $startDate, $endDate),
            'selectedMonth' => $month,
            'selectedYear' => $year
        ];

        return view('admin/activity/index', $data);
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
            'description' => 'permit_empty'
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
            'description' => trim($this->request->getPost('description'))
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
            'description' => 'permit_empty'
        ];

        if ($this->validate($rules)) {
            $updateData = [
                'task' => $this->request->getPost('task'),
                'location' => $this->request->getPost('location'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'activity_date' => $this->request->getPost('activity_date'),
                'description' => $this->request->getPost('description')
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

        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year') ?? date('Y');
        $selectedUser = $this->request->getGet('user');

        $userModel = new UserModel();
        $users = $userModel->getAllhistoryUsers();

        // Tentukan tanggal awal dan akhir berdasarkan bulan dan tahun
        $startDate = null;
        $endDate = null;
        if ($month) {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        $selectedUser = $this->request->getGet('user');
        if ($selectedUser === "all") {
            $selectedUser = null;
        }

        $data = [
            'title' => 'Admin Activities History',
            'activities' => $this->activityModel->getAllAdminActivities($startDate, $endDate, $selectedUser),
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
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');
        $sheet->mergeCells('A5:G5');

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
        $sheet->getStyle('A1:G5')->applyFromArray($headerStyle);

        // Add table headers
        $headers = ['No', 'Date', 'Task', 'Description', 'Location', 'Start Time', 'End Time'];
        $col = 'A';
        $row = 7;
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
        $sheet->getStyle('A7:G7')->applyFromArray($tableHeaderStyle);

        // Add data
        $row = 8;
        foreach ($activities as $i => $activity) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($activity['activity_date'])));
            $sheet->setCellValue('C' . $row, $activity['task']);
            $sheet->setCellValue('D' . $row, $activity['description']);
            $sheet->setCellValue('E' . $row, $activity['location']);
            $sheet->setCellValue('F' . $row, date('H:i', strtotime($activity['start_time'])));
            $sheet->setCellValue('G' . $row, date('H:i', strtotime($activity['end_time'])));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
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
        $data = [
            'activities' => $activities,
            'selectedMonth' => $period,
            'totalActivities' => count($activities),
            'username' => user()->username
        ];

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
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        $sheet->mergeCells('A4:H4');
        $sheet->mergeCells('A5:H5');

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
        $sheet->getStyle('A1:H5')->applyFromArray($headerStyle);

        // Add table headers
        $headers = ['No', 'Date', 'User', 'Task', 'Description', 'Location', 'Start Time', 'End Time'];
        $col = 'A';
        $row = 7;
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
        $sheet->getStyle('A7:H7')->applyFromArray($tableHeaderStyle);

        // Add data or "No Data" message
        $row = 8;
        if (empty($activities)) {
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->setCellValue('A' . $row, 'No activities found for the selected period and filters');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        } else {
            $i = 1;
            foreach ($activities as $activity) {
                $sheet->setCellValue('A' . $row, $i++);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($activity['activity_date'])));
                $sheet->setCellValue('C' . $row, $activity['username']);
                $sheet->setCellValue('D' . $row, $activity['task']);
                $sheet->setCellValue('E' . $row, $activity['description']);
                $sheet->setCellValue('F' . $row, $activity['location']);
                $sheet->setCellValue('G' . $row, date('H:i', strtotime($activity['start_time'])));
                $sheet->setCellValue('H' . $row, date('H:i', strtotime($activity['end_time'])));
                $row++;
            }
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
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
        // Prepare the data for the view
        $data = [
            'activities' => $activities,
            'selectedMonth' => $month ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : $year,
            'totalActivities' => count($activities),
            'generatedDate' => date('d F Y')
        ];

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
}
