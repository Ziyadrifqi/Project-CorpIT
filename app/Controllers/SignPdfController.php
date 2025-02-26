<?php

namespace App\Controllers;

use App\Models\SignPdfModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class SignPdfController extends Controller
{
    protected $signPdfModel;
    protected $userModel;
    protected $pdfPath = '/public/sign/';

    public function __construct()
    {
        $this->signPdfModel = new SignPdfModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedUser = $this->request->getGet('user') ?? 'all';

        // Konversi format bulan dari YYYY-MM menjadi "January_2025"
        $selectedMonthFormatted = date('F_Y', strtotime($selectedMonth . '-01'));

        // Get all users for the dropdown
        $users = $this->userModel->getAllhistoryUsers();

        // Get filtered documents using the new model method
        $pdfs = $this->signPdfModel->getSignedDocuments($selectedMonthFormatted, $selectedUser);

        // Check if PDF files exist and add file path
        foreach ($pdfs as &$pdf) {
            $pdfFilePath = 'public/sign/' . $pdf['name_pdf'];
            $pdf['file_exists'] = file_exists(FCPATH . $pdfFilePath);
            $pdf['file_path'] = base_url($pdfFilePath);

            // Format the PDF name for display
            $originalName = $pdf['name_pdf'];
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $nameParts = explode('_', $nameWithoutExt);
            $pdf['display_name'] = ucwords(implode(' ', $nameParts));

            // Store the readable period format for display
            $periodeParts = explode('_', $pdf['periode']);
            if (count($periodeParts) == 2) {
                $month = $periodeParts[0];
                $year = $periodeParts[1];
                $pdf['display_periode'] = $month . ' ' . $year;
            } else {
                $pdf['display_periode'] = $pdf['periode'];
            }
        }

        $data = [
            'title' => 'Signed Documents',
            'pdfs' => $pdfs,
            'users' => $users,
            'selectedMonth' => $selectedMonth,
            'selectedUser' => $selectedUser
        ];

        return view('admin/activity/sign', $data);
    }
}
