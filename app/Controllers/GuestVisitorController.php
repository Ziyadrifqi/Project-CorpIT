<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class GuestVisitorController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Guest Visitor Management',
            'guests' => [] // Isi dengan data dari API jika diperlukan
        ];
        return view('guest_visitor/list', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Guest Visitor'
        ];
        return view('guest_visitor/create', $data);
    }

    private function generateGuestName()
    {
        // Get current date in YYYYMMDD format
        $currentDate = date('Ymd');

        // Get the latest guest number from the API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.ap.central.arubanetworks.com/guest/v1/portals/fb276af6-96e1-4bef-bfac-41874211286/visitors',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: Bearer nYH6H4N5D1VHdfvrMc2I5sx5rvNZdUI'
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $guests = json_decode($response, true);

        // Find the highest number used today
        $maxNumber = 0;
        if ($guests && is_array($guests)) {
            foreach ($guests as $guest) {
                if (preg_match('/GUEST' . $currentDate . '_(\d{3})/', $guest['name'], $matches)) {
                    $maxNumber = max($maxNumber, intval($matches[1]));
                }
            }
        }

        // Generate new number
        $nextNumber = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);

        return 'GUEST' . $currentDate . '_' . $nextNumber;
    }

    public function save()
    {
        // Validasi input
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'valid_till_days' => 'required|numeric|greater_than[0]|less_than[366]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Generate guest name
        $guestName = $this->generateGuestName();

        // Siapkan data untuk API
        $payload = [
            'is_enabled' => $this->request->getPost('is_enabled') ? true : false,
            'valid_till_no_limit' => false,
            'notify' => $this->request->getPost('notify') ? true : false,
            'notify_to' => 'email',
            'name' => $guestName,  // Use generated name instead of user input
            'status' => true,
            'user' => [
                'email' => [
                    'newkey' => $this->request->getPost('email')
                ]
            ],
            'password' => $this->request->getPost('password'),
            'valid_till_days' => (int)$this->request->getPost('valid_till_days')
        ];

        // Inisialisasi cURL
        $curl = curl_init();

        // Set opsi cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.ap.central.arubanetworks.com/guest/v1/portals/fb276af6-96e1-4bef-bfac-41874211286/visitors',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer nYH6H4N5D1VHdfvrMc2I5sx5rvNZdUI'
            ]
        ]);

        // Eksekusi cURL
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        // Handle response
        if ($err) {
            return redirect()->back()->withInput()
                ->with('error', 'Error: ' . $err);
        }

        $result = json_decode($response);

        if (isset($result->error)) {
            return redirect()->back()->withInput()
                ->with('error', 'API Error: ' . $result->error->message);
        }

        return redirect()->to('guest-visitor')
            ->with('success', 'Guest visitor created successfully');
    }
}
