<?php

namespace App\Controllers\Absensi;

use App\Controllers\BaseController;
use App\Models\AdminActivityModel;
use App\Models\Absensi\AbsensiModel;
use App\Models\UserModel;

class OvertimeMonitoringController extends BaseController
{
    protected $activityModel;
    protected $absensiModel;
    protected $userModel;

    public function __construct()
    {
        $this->activityModel = new AdminActivityModel();
        $this->absensiModel = new AbsensiModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $month = $this->request->getGet('month') ?? date('Y-m');
        $selectedUser = $this->request->getGet('user');
        $isFiltered = $this->request->getGet('filtered') === 'true';

        // Get users for dropdown
        $users = $this->userModel->getAllhistoryUsers();

        // Only get combined data if the form has been filtered
        $combinedData = [];
        if ($isFiltered && $selectedUser) {
            // Calculate start and end dates
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
            $combinedData = $this->getCombinedData($startDate, $endDate, $selectedUser);
        }

        $data = [
            'title' => 'Overtime Monitoring',
            'activities' => $combinedData,
            'users' => $users,
            'selectedUser' => $selectedUser,
            'selectedMonth' => $month,
            'isFiltered' => $isFiltered
        ];

        return view('absensi/superadmin/overtime', $data);
    }

    private function getCombinedData($startDate, $endDate, $selectedUser)
    {
        // Get admin activities
        $activities = $this->activityModel->getAllAdminActivities($startDate, $endDate, $selectedUser);

        // Get attendance data
        $attendance = $this->absensiModel->getSuperAdminHistory($startDate, $endDate, null, $selectedUser);

        // Combine and format data
        $combinedData = [];

        // Process admin activities
        foreach ($activities as $activity) {
            $combinedData[] = [
                'date' => $activity['activity_date'],
                'task' => $activity['task'],
                'diary_activity' => $activity['description'],
                'start_time' => $activity['start_time'],
                'end_time' => $activity['end_time'],
                'ticket_number' => $activity['no_tiket'],
                'username' => $activity['username'],
                'source' => 'activity'
            ];
        }

        // Process attendance
        foreach ($attendance as $item) {
            $combinedData[] = [
                'date' => $item['tanggal'],
                'task' => $item['judul_kegiatan'],
                'diary_activity' => $item['kegiatan_harian'],
                'start_time' => $item['jam_masuk'],
                'end_time' => $item['jam_keluar'],
                'ticket_number' => $item['no_tiket'],
                'username' => $item['user_name'],
                'source' => 'attendance'
            ];
        }

        // Sort by date and start time
        usort($combinedData, function ($a, $b) {
            $dateCompare = strtotime($a['date']) - strtotime($b['date']);
            if ($dateCompare == 0) {
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            }
            return $dateCompare;
        });

        return $combinedData;
    }
}
