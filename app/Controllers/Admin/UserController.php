<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        helper('acl');
    }

    /**
     * Menampilkan daftar semua pengguna. (INDEX)
     */
    public function index()
    {
        // ... (Kode index yang sudah ada) ...
        if (!has_permission('user-manage')) {
            return redirect()->to(base_url('admin'))->with('error', 'Akses ditolak.');
        }

        $users = $this->userModel
                      ->select('users.*, roles.name AS role_name')
                      ->join('roles', 'roles.id = users.role_id', 'left')
                      ->findAll();
                      
        $roles = $this->roleModel->findAll();

        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $users,
            'roles' => $roles,
        ];

        return view('admin/users/index', $data);
    }

    // ===================================================
    // METHOD BARU UNTUK MENAMBAH PENGGUNA (CREATE & STORE)
    // ===================================================

    /**
     * Menampilkan form untuk menambahkan pengguna baru. (CREATE)
     */
    public function create()
    {
        // Cek Hak Akses
        if (!has_permission('user-manage')) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk menambah pengguna.');
        }

        $roles = $this->roleModel->findAll();

        $data = [
            'title' => 'Tambah Pengguna Baru',
            'roles' => $roles,
            'validation' => \Config\Services::validation()
        ];

        // Kita akan buat view 'admin/users/create' di langkah selanjutnya
        return view('admin/users/create', $data); 
    }

    /**
     * Menyimpan pengguna baru ke database. (STORE)
     */
    public function store()
    {
        // Cek Hak Akses
        if (!has_permission('user-manage')) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Akses ditolak.');
        }

        // 1. Tentukan Aturan Validasi
        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role_id'  => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pengguna. Periksa kembali input Anda.');
        }

        // 2. Ambil Data dan Hash Password
        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), // Wajib di-hash
            'role_id'  => (int)$this->request->getPost('role_id'),
            'status'   => 'active', // Default status
        ];

        // 3. Simpan ke Database
        try {
            $this->userModel->insert($data);
            return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna baru **' . esc($data['username']) . '** berhasil ditambahkan.');
        } catch (\Exception $e) {
            log_message('error', 'Gagal menyimpan pengguna: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan server saat menyimpan data.');
        }
    }
    
    /**
     * Memperbarui Role atau Status pengguna (via AJAX). (UPDATE)
     */
    public function updateUser(int $userId)
    {
        // Cek Hak Akses
        if (!has_permission('user-manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }
        
        $roleId = $this->request->getPost('role_id');
        $status = $this->request->getPost('status');

        // Pastikan Admin tidak bisa mengubah role/status dirinya sendiri (kecuali Anda mau)
        if ($userId == session()->get('user_id')) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Anda tidak dapat mengubah status atau peran Anda sendiri.']);
        }

        $updateData = [];

        if ($roleId) {
            $updateData['role_id'] = (int)$roleId;
        }

        if ($status) {
            $updateData['status'] = $status;
        }

        if (empty($updateData)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Tidak ada data yang dikirim untuk diperbarui.']);
        }

        try {
            $this->userModel->update($userId, $updateData);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil diperbarui.']);
            
        } catch (\Exception $e) {
            log_message('error', 'Gagal update pengguna: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal memperbarui pengguna karena kesalahan database.']);
        }
    }
    
    // Metode create, edit, delete tidak diperlukan karena manajemen hanya berfokus pada status/role.
}