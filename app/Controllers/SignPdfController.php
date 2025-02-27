<?php

namespace App\Controllers;

use App\Models\SignPdfModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use ZipArchive;

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

    // Existing index method for superadmin
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

    // method to download PDFs as a ZIP file
    public function downloadPdfsZip()
    {
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $selectedUser = $this->request->getGet('user') ?? 'all';

        // Konversi format bulan dari YYYY-MM menjadi "January_2025"
        $selectedMonthFormatted = date('F_Y', strtotime($selectedMonth . '-01'));

        // Get filtered documents
        $pdfs = $this->signPdfModel->getSignedDocuments($selectedMonthFormatted, $selectedUser);

        // If no PDFs found
        if (empty($pdfs)) {
            return redirect()->back()->with('error', 'No PDF documents found for the selected criteria.');
        }

        // Create a temporary file for the zip
        $zipFilename = 'signed_documents_' . $selectedMonthFormatted . '.zip';
        $zipPath = WRITEPATH . 'temp/' . $zipFilename;

        // Create zip archive
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'Unable to create ZIP file.');
        }

        $filesAdded = 0;

        // Add PDF files to the zip
        foreach ($pdfs as $pdf) {
            // Add the same display_periode processing that exists in the index method
            $periodeParts = explode('_', $pdf['periode']);
            if (count($periodeParts) == 2) {
                $month = $periodeParts[0];
                $year = $periodeParts[1];
                $pdf['display_periode'] = $month . ' ' . $year;
            } else {
                $pdf['display_periode'] = $pdf['periode'];
            }

            $pdfFilePath = FCPATH . 'public/sign/' . $pdf['name_pdf'];

            if (file_exists($pdfFilePath)) {
                // Use a more readable name in the zip
                $zipEntryName = $pdf['fullname'] . '_' . $pdf['display_periode'] . '.pdf';
                $zip->addFile($pdfFilePath, $zipEntryName);
                $filesAdded++;
            }
        }

        $zip->close();

        if ($filesAdded === 0) {
            // No valid files were added to the zip
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            return redirect()->back()->with('error', 'No valid PDF files found to download.');
        }

        // Set download headers
        $response = $this->response
            ->download($zipPath, null)
            ->setContentType('application/zip');

        // Delete the temporary file after download (using register_shutdown_function)
        register_shutdown_function(function () use ($zipPath) {
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        });

        return $response;
    }

    // method for admin users
    public function admin_sign()
    {
        // Get current user ID from session
        $userId = user()->id;

        // Get admin user data
        $user = $this->userModel->getAdminUserById($userId);

        // Get filter parameters - only period filter for admin
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');

        // Convert month format from YYYY-MM to "Month_Year"
        $selectedMonthFormatted = date('F_Y', strtotime($selectedMonth . '-01'));

        // Get filtered signed overtime documents for this admin only
        $pdfs = $this->signPdfModel->getSignedDocuments($selectedMonthFormatted, $userId);

        // Process PDFs for display
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
            'title' => 'My Signed Overtime Documents',
            'pdfs' => $pdfs,
            'selectedMonth' => $selectedMonth,
            'user' => $user
        ];

        return view('admin/activity/sign_admin', $data);
    }
}
