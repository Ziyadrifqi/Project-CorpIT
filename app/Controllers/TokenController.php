<?php

namespace App\Controllers;

use CodeIgniter\HTTP\CURLRequest;

class TokenController extends BaseController
{
    protected $client;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();
    }

    public function index()
    {
        $data = [
            'title' => 'Refresh Token Management',
            'validation' => \Config\Services::validation(),
            'token_data' => session()->get('token_data')
        ];

        // Cek jika token sudah kadaluarsa
        $tokenData = session()->get('token_data');
        if ($tokenData && isset($tokenData['expires_in'])) {
            $expiresIn = $tokenData['expires_in'];
            $tokenTimestamp = session()->get('token_timestamp');

            // Jika token sudah kadaluarsa, lakukan refresh
            if (time() > ($tokenTimestamp + $expiresIn)) {
                // Panggil fungsi refresh token
                $this->refresh();
            }
        }

        return view('token/refresh', $data);
    }

    public function refresh()
    {
        // Validasi input
        $rules = [
            'current_token' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $refreshToken = $this->request->getPost('current_token');

        $baseUrl = 'https://api.ap.central.arubanetworks.com/oauth2/token';

        $queryParams = [
            'client_id' => getenv('OAUTH_CLIENT_ID'),
            'client_secret' => getenv('OAUTH_CLIENT_SECRET'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];

        $url = $baseUrl . '?' . http_build_query($queryParams);

        $headers = [
            'accept' => 'application/json',
            'authorization' => 'Bearer ' . getenv('BEARER_TOKEN')
        ];

        try {
            // Request untuk refresh token
            $response = $this->client->request('GET', $url, ['headers' => $headers]);
            $result = json_decode($response->getBody(), true);

            if (isset($result['access_token'])) {
                session()->setFlashdata('success', 'Token refreshed successfully');
                session()->set('token_data', [
                    'refresh_token' => $result['refresh_token'],
                    'token_type' => $result['token_type'],
                    'access_token' => $result['access_token'],
                    'expires_in' => $result['expires_in']
                ]);
                session()->set('token_timestamp', time()); // Set timestamp saat token diterima
            } else {
                session()->setFlashdata('error', 'Failed to refresh token');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', $e->getMessage());
        }

        return redirect()->back();
    }
}
