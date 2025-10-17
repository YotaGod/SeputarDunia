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
        return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
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

    // Redirect ke dashboard admin dengan pesan sukses
    return redirect()->to(base_url('admin'))->with('success', 'Selamat datang, ' . $user['username']);
}

    // METHOD UNTUK LOGOUT
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url())->with('success', 'Anda telah berhasil logout.');
    }
}