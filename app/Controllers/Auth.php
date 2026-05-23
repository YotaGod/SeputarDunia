<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // METHOD UNTUK MENAMPILKAN FORM LOGIN (GET)
    public function login()
    {
        helper('form'); 
        if (session()->get('isLoggedIn')) {
            $role = strtolower(session()->get('role_name'));
            if ($role === 'pengunjung' || $role === 'pengunjung berlangganan') {
                return redirect()->to(base_url());
            }
            return redirect()->to(base_url('admin'));
        }
        return view('auth/login');
    }

    // app/Controllers/Auth.php (Potongan method attemptLogin)

    public function attemptLogin()
{
    $rules = [
        'email' => 'required|valid_email',
        'password' => 'required',
    ];

    if (!$this->validate($rules)) {
        $errorString = implode('<br>', $this->validator->getErrors());
        return redirect()->back()->withInput()->with('error', $errorString);
    }

    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    // Cari user berdasarkan email
    $user = $this->userModel->getUserByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
        return redirect()->back()->withInput()->with('error', 'Email atau Password salah.');
    }

    log_message('debug', 'Login attempt: ' . $email);
    log_message('debug', 'User found: ' . json_encode($user));

    // Jika user ditemukan dan password benar, set session
    $sessionData = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role_id' => $user['role_id'],
        'role_name' => $user['role_name'], // Dari join dengan tabel roles
        'isLoggedIn' => true
    ];

    session()->set($sessionData);

    $roleStr = strtolower($user['role_name']);
    if ($roleStr === 'pengunjung' || $roleStr === 'pengunjung berlangganan') {
        return redirect()->to(base_url())->with('success', 'Selamat datang, ' . $user['username']);
    }

    // Redirect ke dashboard admin dengan pesan sukses
    return redirect()->to(base_url('admin'))->with('success', 'Selamat datang, ' . $user['username']);
}

    // METHOD UNTUK LOGOUT
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url())->with('success', 'Anda telah berhasil logout.');
    }

    // GOOGLE LOGIN METHODS
    private function getGoogleClient()
    {
        if (!class_exists('\Google_Client')) {
            throw new \Exception('Google API Client belum di-install. Jalankan composer require google/apiclient');
        }
        $client = new \Google_Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID') ?: 'dummy_client_id');
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET') ?: 'dummy_client_secret');
        $client->setRedirectUri(base_url('auth/google/callback'));
        $client->addScope("email");
        $client->addScope("profile");
        return $client;
    }

    public function googleLogin()
    {
        try {
            $client = $this->getGoogleClient();
            return redirect()->to($client->createAuthUrl());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function googleCallback()
    {
        try {
            $client = $this->getGoogleClient();
            $code = $this->request->getGet('code');
            
            if (!$code) {
                return redirect()->to(base_url('login'))->with('error', 'Google login dibatalkan.');
            }

            $token = $client->fetchAccessTokenWithAuthCode($code);
            $client->setAccessToken($token['access_token']);

            $googleService = new \Google_Service_Oauth2($client);
            $googleUser = $googleService->userinfo->get();

            $email = $googleUser->email;
            $name = $googleUser->name;

            // Cek apakah user sudah ada
            $user = $this->userModel->getUserByEmail($email);

            if (!$user) {
                // Buat user baru jika belum ada
                $newUserId = $this->userModel->insert([
                    'username' => $name,
                    'email' => $email,
                    'password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), // Random password
                    'role_id' => 3 // ID 3 biasanya untuk Pengunjung
                ]);
                $user = $this->userModel->getUserByEmail($email);
            }

            // Set session
            $sessionData = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role_id' => $user['role_id'],
                'role_name' => $user['role_name'] ?? 'Pengunjung', 
                'isLoggedIn' => true
            ];

            session()->set($sessionData);

            $roleStr = strtolower($sessionData['role_name']);
            if ($roleStr === 'pengunjung' || $roleStr === 'pengunjung berlangganan') {
                return redirect()->to(base_url())->with('success', 'Berhasil login dengan Google. Selamat datang, ' . $user['username']);
            }

            return redirect()->to(base_url('admin'))->with('success', 'Selamat datang, ' . $user['username']);

        } catch (\Exception $e) {
            return redirect()->to(base_url('login'))->with('error', 'Gagal login via Google: ' . $e->getMessage());
        }
    }
}