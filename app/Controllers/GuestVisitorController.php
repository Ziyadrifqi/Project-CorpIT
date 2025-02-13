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
        $this->tokenModel = new TokenModel();
        $this->guestVisitorModel = new GuestVisitorModel();
        $this->session = Services::session();
        $this->validation = Services::validation();
    }

    public function index()
    {
        $data = [
            'title' => 'Guest Visitor Management',
            'guests' => $this->guestVisitorModel->getGuestsWithUserInfo()
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
        // Validation rules
        $rules = [
            'password' => 'required|min_length[6]',
            'valid_days' => 'required|integer|greater_than[0]|less_than_equal_to[30]',
            'phone' => 'required|min_length[10]|max_length[15]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Generate guest name
            $currentDate = date('dmY');
            $lastGuest = $this->guestVisitorModel->orderBy('id', 'DESC')->first();
            $counter = $lastGuest ? (intval(substr($lastGuest['guest_name'], -3)) + 1) : 1;
            $guestName = sprintf("GUEST%s_%03d", $currentDate, $counter);

            // Get valid token using ensureValidToken
            $token = $this->ensureValidToken();

            // Create guest via API
            $postData = $this->request->getPost();
            $apiResponse = $this->createGuestViaApi($token['access_token'], [
                'name' => $guestName,
                'phone' => $postData['phone'],
                'password' => $postData['password'],
                'valid_days' => $postData['valid_days']
            ]);

            if (!$apiResponse) {
                throw new \Exception('Failed to create guest in the external system.');
            }

            if (!isset($apiResponse['id'])) {
                throw new \Exception('Invalid response from the external system.');
            }

            // Save to database
            $this->guestVisitorModel->insert([
                'guest_name' => $guestName,
                'guest_id' => $apiResponse['id'],
                'user_id' => $this->session->get('user_id'),
                'status' => 1,
                'phone' => $postData['phone'],
                'valid_until' => date('Y-m-d H:i:s', strtotime("+{$postData['valid_days']} days"))
            ]);

            return redirect()->to('/guest-visitor')->with('success', 'Guest visitor created successfully');
        } catch (\Exception $e) {
            log_message('error', '[GuestVisitor] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create guest visitor: ' . $e->getMessage());
        }
    }

    private function refreshToken()
    {
        $currentToken = $this->tokenModel->getLatestToken();

        // Debug log untuk cek token dari database
        log_message('debug', 'Current token from DB: ' . json_encode($currentToken));

        if (!$currentToken || empty($currentToken['refresh_token'])) {
            log_message('error', '[Token Refresh] No valid refresh token found');
            return null;
        }

        $url = "https://api-ap.central.arubanetworks.com/oauth2/token";
        $params = [
            'client_id' => 'uEDP1BABfpXMefVwonvHAarx4HwE7AXu',
            'client_secret' => 'C3t4oi1JmQ7kyQiYKlpFQxZAvGQANKrO',
            'grant_type' => 'refresh_token',
            'refresh_token' => $currentToken['refresh_token']
        ];

        try {
            // Debug log untuk request
            log_message('debug', 'Attempting token refresh with params: ' . json_encode($params));

            $curl = Services::curlrequest();
            $response = $curl->post($url, [
                'form_params' => $params,
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'http_errors' => false,
                'debug' => true
            ]);

            $statusCode = $response->getStatusCode();
            $result = json_decode($response->getBody(), true);

            // Debug log untuk response
            log_message('debug', 'Token refresh response status: ' . $statusCode);
            log_message('debug', 'Token refresh response body: ' . json_encode($result));

            if ($statusCode !== 200) {
                throw new \Exception("Token refresh failed with status {$statusCode}: " . json_encode($result));
            }

            // Save new token
            $tokenData = [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'expires_in' => time() + $result['expires_in']
            ];

            $saved = $this->tokenModel->saveNewToken($tokenData);

            // Debug log untuk saving
            log_message('debug', 'Token save result: ' . ($saved ? 'success' : 'failed'));

            return $tokenData;
        } catch (\Exception $e) {
            log_message('error', '[Token Refresh] Error: ' . $e->getMessage());
            return null;
        }
    }

    private function createGuestViaApi($token, $data)
    {
        $url = "https://api-ap.central.arubanetworks.com/guest/v1/portals/fb276af6-96e1-4bef-bfac-4187421f2865/visitors";

        $nextId = uniqid();
        $payload = [
            'id' => $nextId,
            'is_enabled' => true,
            'valid_till_no_limit' => false,
            'notify' => true,
            'notify_to' => 'phone',
            'name' => $data['name'],
            'status' => true,
            'user' => [
                'phone' => $data['phone']
            ],
            'password' => $data['password'],
            'valid_till_days' => intval($data['valid_days'])
        ];

        try {
            $curl = Services::curlrequest();
            $response = $curl->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'debug' => true,
                'http_errors' => false // Don't throw exceptions for HTTP errors
            ]);

            $statusCode = $response->getStatusCode();
            $result = json_decode($response->getBody(), true);

            // Log the full response for debugging
            log_message('debug', 'API Response Status: ' . $statusCode);
            log_message('debug', 'API Response Body: ' . json_encode($result));

            if ($statusCode !== 200) {
                $errorMessage = isset($result['description']) ? $result['description'] : (isset($result['message']) ? $result['message'] : 'Unknown error');
                throw new \Exception("API returned {$statusCode}: {$errorMessage}");
            }

            if (!isset($result['id'])) {
                log_message('error', 'Invalid API response format: ' . json_encode($result));
                throw new \Exception('Invalid response format from API');
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', '[Guest Creation] Error: ' . $e->getMessage());
            log_message('error', '[Guest Creation] Request payload: ' . json_encode($payload));
            throw new \Exception('Failed to communicate with the external system: ' . $e->getMessage());
        }
    }

    private function isTokenValid($token)
    {
        if (!$token || empty($token['access_token']) || empty($token['expires_in'])) {
            return false;
        }

        // Add a 5-minute buffer to prevent edge cases
        $bufferTime = 300; // 5 minutes in seconds
        return ($token['expires_in'] > (time() + $bufferTime));
    }

    private function ensureValidToken()
    {
        $token = $this->tokenModel->getLatestToken();

        if (!$this->isTokenValid($token)) {
            $token = $this->refreshToken();
            if (!$token) {
                throw new \Exception('Failed to obtain valid authentication token');
            }
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
