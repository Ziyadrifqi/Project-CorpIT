<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TokenModel;
use App\Models\GuestVisitorModel;
use Config\Services;

class GuestVisitorController extends Controller
{
    protected $tokenModel;
    protected $guestVisitorModel;
    protected $session;
    protected $validation;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->tokenModel = new TokenModel();
        $this->guestVisitorModel = new GuestVisitorModel();
        $this->session = Services::session();
        $this->validation = Services::validation();
    }

    public function index()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $userId = user_id(); // Get current logged in user's ID

        // Modified to only get guests for current user
        $guests = $this->guestVisitorModel->getGuestsByUserId($userId);

        $data = [
            'title' => 'My Guest Visitors',
            'guests' => $guests
        ];

        return view('guest_visitor/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Guest Visitor',
            'validation' => $this->validation
        ];
        return view('guest_visitor/create', $data);
    }

    public function store()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        // Validation rules
        $rules = [
            'password' => 'required|min_length[6]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'email' => 'required|valid_email|min_length[10]|max_length[35]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Generate guest name with daily counter reset
            $currentDate = date('dmY');
            $lastGuest = $this->guestVisitorModel->orderBy('id', 'DESC')->first();

            // Initialize counter
            $counter = 1;

            // Check if there's a last guest and if it's from today
            if ($lastGuest) {
                $lastGuestDate = substr($lastGuest['guest_name'], 5, 8); // Extract date from GUEST{date}_{counter}
                if ($lastGuestDate === $currentDate) {
                    // If it's the same day, increment the counter
                    $counter = (intval(substr($lastGuest['guest_name'], -3)) + 1);
                }
                // If it's a different day, counter remains 1
            }

            $guestName = sprintf("GUEST%s_%03d", $currentDate, $counter);

            // Get valid token
            $token = $this->ensureValidToken(); // Token will be refreshed here if needed

            // Save the refresh token
            $this->tokenModel->saveNewToken([
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'expires_in' => $token['expires_in'],
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Create guest via API
            $postData = $this->request->getPost();
            $apiResponse = $this->createGuestViaApi($token['access_token'], [
                'name' => $guestName,
                'phone' => $postData['phone'],
                'email' => $postData['email'],
                'password' => $postData['password']
            ]);

            if (!$apiResponse) {
                throw new \Exception('Failed to create guest in the external system.');
            }

            $userId = user_id();

            // Save to database (valid_until default 1 hari sudah diatur dalam database)
            $inserted = $this->guestVisitorModel->insert([
                'guest_name' => $guestName,
                'user_id' => $userId,
                'status' => 1,
                'phone' => $postData['phone'],
                'password' => $postData['password'],
                'email' => $postData['email']
            ]);

            if ($inserted) {
                log_message('debug', 'Berhasil insert guest dengan ID: ' . $inserted);
                return redirect()->to('/guest-visitor')->with('success', 'Guest visitor created successfully');
            } else {
                throw new \Exception('Failed to insert guest data');
            }
        } catch (\Exception $e) {
            log_message('error', '[GuestVisitor] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create guest visitor: ' . $e->getMessage());
        }
    }

    private function refreshToken()
    {
        try {
            $currentToken = $this->tokenModel->getLatestToken();
            if (!$currentToken || empty($currentToken['refresh_token'])) {
                throw new \Exception('No valid refresh token found');
            }

            $url = "https://api-ap.central.arubanetworks.com/oauth2/token";
            $params = [
                'client_id' => 'uEDP1BABfpXMefVwonvHAarx4HwE7AXu',
                'client_secret' => 'C3t4oi1JmQ7kyQiYKlpFQxZAvGQANKrO',
                'grant_type' => 'refresh_token',
                'refresh_token' => $currentToken['refresh_token']
            ];

            $curl = Services::curlrequest();
            $response = $curl->post($url, [
                'form_params' => $params,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $result = json_decode($response->getBody(), true);

            if ($statusCode !== 200) {
                throw new \Exception("Token refresh failed with status {$statusCode}: " . json_encode($result));
            }

            // Simpan nilai expires_in langsung dari API
            $tokenData = [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'expires_in' => $result['expires_in'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->tokenModel->saveNewToken($tokenData)) {
                throw new \Exception('Failed to save new token to database');
            }

            return $tokenData;
        } catch (\Exception $e) {
            log_message('error', '[Token Refresh] Error: ' . $e->getMessage());
            return null;
        }
    }

    private function createGuestViaApi($token, $data)
    {
        $url = "https://api-ap.central.arubanetworks.com/guest/v1/portals/fb276af6-96e1-4bef-bfac-4187421f2865/visitors";

        // Generate a UUID for the guest ID
        $id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );

        // Make sure name is explicitly included in both places
        $payload = [
            'name' => $data['name'], // Add name at root level
            'visitor' => [
                'name' => $data['name'], // Add name in visitor object
                'password' => $data['password'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'is_enabled' => true,
                'valid_till_no_limit' => false,
                'notify' => true,
                'notify_to' => ['phone'],
                'status' => true
            ]
        ];

        try {
            $curl = Services::curlrequest();

            // Add detailed request logging
            log_message('debug', 'Guest Creation Request - URL: ' . $url);
            log_message('debug', 'Guest Creation Request - Payload: ' . json_encode($payload, JSON_PRETTY_PRINT));
            log_message('debug', 'Guest Creation Request - Token: ' . substr($token, 0, 20) . '...');

            $response = $curl->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody(), true);

            // Enhanced response logging
            log_message('debug', 'Guest Creation Response - Status: ' . $statusCode);
            log_message('debug', 'Guest Creation Response - Body: ' . json_encode($responseBody, JSON_PRETTY_PRINT));

            if ($statusCode !== 200) {
                $errorDetail = isset($responseBody['description'])
                    ? $responseBody['description']
                    : (isset($responseBody['error_description'])
                        ? $responseBody['error_description']
                        : json_encode($responseBody));

                throw new \Exception("API Error (Status {$statusCode}): {$errorDetail}");
            }

            return $responseBody;
        } catch (\Exception $e) {
            log_message('error', 'Guest Creation API Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Failed to create guest: ' . $e->getMessage());
        }
    }

    private function isTokenValid($token)
    {
        if (!$token || empty($token['access_token']) || empty($token['expires_in'])) {
            return false;
        }

        // Buffer 5 menit
        $bufferTime = 300;

        // Hitung waktu expire
        $createdTime = strtotime($token['created_at']);
        $expiryTime = $createdTime + $token['expires_in'];

        return ($expiryTime > (time() + $bufferTime));
    }

    private function ensureValidToken()
    {
        $token = $this->tokenModel->getLatestToken();
        log_message('debug', 'Current token: ' . json_encode($token));

        if (!$this->isTokenValid($token)) {
            log_message('debug', 'Token invalid or expired, refreshing...');
            $token = $this->refreshToken();
            if (!$token) {
                throw new \Exception('Failed to obtain valid authentication token');
            }
            log_message('debug', 'New token obtained: ' . json_encode($token));
        }

        return $token;
    }

    public function history()
    {
        // Get filter parameters
        $monthFilter = $this->request->getGet('month') ?? date('Y-m');
        $userFilter = $this->request->getGet('user') ?? 'all';

        // Base query
        $query = $this->guestVisitorModel->select('guest_visitors.*, users.username as created_by')
            ->join('users', 'users.id = guest_visitors.user_id');

        // Apply filters
        if ($monthFilter) {
            $query->where("DATE_FORMAT(guest_visitors.created_at, '%Y-%m')", $monthFilter);
        }

        if ($userFilter !== 'all') {
            $query->where('guest_visitors.user_id', $userFilter);
        }

        // Get users for filter dropdown
        $userModel = new \App\Models\UserModel();
        $users = $userModel->getAllhistoryUsers();

        $data = [
            'title' => 'Guest Visitor History',
            'guests' => $query->orderBy('guest_visitors.created_at', 'DESC')->findAll(),
            'users' => $users,
            'selectedMonth' => $monthFilter,
            'selectedUser' => $userFilter
        ];

        return view('guest_visitor/history', $data);
    }
}
