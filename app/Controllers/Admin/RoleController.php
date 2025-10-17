<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;          // <-- PERBAIKAN: Tambahkan use statement
use App\Models\PermissionModel;     // <-- PERBAIKAN: Tambahkan use statement
use App\Models\RolePermissionModel;
use App\Models\UserModel;
use CodeIgniter\Database\ConnectionInterface; // Tambahkan use statement untuk tipe hinting

class RoleController extends BaseController
{
    // 1. DEKLARASI PROPERTI
    protected $roleModel;
    protected $permissionModel;
    protected $rolePermissionModel;
    protected $userModel;
    protected $db; // <-- PERBAIKAN: Deklarasi properti database

    public function __construct()
    {
        // 2. INISIALISASI PROPERTI (MENGHILANGKAN ERROR UNDEFINED MODEL)
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect(); // <-- PERBAIKAN: Inisialisasi database
        
        helper('acl'); // Memuat helper
    }

    /**
     * Menampilkan daftar semua peran dan hak aksesnya.
     */
    public function index()
    {
        if (!has_permission('role-manage')) {
            return redirect()->to(base_url('admin'))->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengelola peran.');
        }

        $roles = $this->roleModel->findAll();
        $permissions = $this->permissionModel->findAll();
        
        // --- START LOGIKA PENGELOMPOKAN ---
        $groupedPermissions = [];
        $groups = [
            'article' => 'Manajemen Artikel',
            'user' => 'Manajemen Pengguna & Peran',
            'system' => 'Konfigurasi Sistem',
            'frontend' => 'Akses Pengunjung (Frontend)',
        ];

        foreach ($permissions as $perm) {
            $key = $perm['key'];
            
            // Menentukan Grup berdasarkan prefix key
            if (str_starts_with($key, 'article')) {
                $groupKey = 'article';
            } elseif (str_starts_with($key, 'user') || str_starts_with($key, 'role')) {
                $groupKey = 'user';
            } elseif (str_starts_with($key, 'category') || str_starts_with($key, 'comment')) {
                $groupKey = 'system';
            } elseif (str_starts_with($key, 'can') || str_starts_with($key, 'access')) {
                $groupKey = 'frontend';
            } else {
                $groupKey = 'system'; // Default jika tidak cocok
            }
            
            $groupedPermissions[$groupKey][] = $perm;
        }
        // --- END LOGIKA PENGELOMPOKAN ---
        
        // Ambil semua hak akses yang terhubung (role_id => [permission_id1, permission_id2, ...])
        $currentPermissions = $this->rolePermissionModel->select('role_id, permission_id')->findAll();
        $rolePermissionsMap = [];
        foreach ($currentPermissions as $rp) {
            $rolePermissionsMap[$rp['role_id']][] = $rp['permission_id'];
        }

        $data = [
            'title' => 'Manajemen Peran & Hak Akses',
            'roles' => $roles,
            'groupedPermissions' => $groupedPermissions, // Mengirim data yang sudah dikelompokkan
            'permissionGroups' => $groups, // Mengirim nama grup
            'rolePermissionsMap' => $rolePermissionsMap,
        ];

        return view('admin/roles/index', $data);
    }
    
    /**
     * Menyimpan pembaruan hak akses peran (via AJAX/POST).
     */
    public function savePermissions()
    {
        // Cek Hak Akses
        if (!has_permission('role-manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }
        
        $roleId = $this->request->getPost('role_id');
        $permissionIds = $this->request->getPost('permissions') ?? []; // Array ID permission yang dipilih
        
        if (!$roleId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'ID Peran tidak valid.']);
        }

        $this->db->transBegin(); // <-- PERBAIKAN: Menggunakan $this->db
        try {
            // 1. Hapus semua hak akses lama untuk peran ini
            $this->rolePermissionModel->where('role_id', $roleId)->delete();
            
            // 2. Masukkan hak akses baru
            if (!empty($permissionIds)) {
                $batchData = [];
                foreach ($permissionIds as $pId) {
                    $batchData[] = [
                        'role_id' => $roleId,
                        'permission_id' => (int)$pId,
                    ];
                }
                $this->rolePermissionModel->insertBatch($batchData);
            }
            
            $this->db->transCommit(); // <-- PERBAIKAN: Menggunakan $this->db
            return $this->response->setJSON(['success' => true, 'message' => 'Hak akses berhasil diperbarui.']);
            
        } catch (\Exception $e) {
            $this->db->transRollback(); // <-- PERBAIKAN: Menggunakan $this->db
            log_message('error', 'Gagal update permissions: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal memperbarui hak akses.']);
        }
    }

    /**
     * Menyimpan peran baru yang ditambahkan Admin.
     */
    public function storeRole()
    {
        // 1. Cek Hak Akses
        if (!has_permission('role-manage')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Akses ditolak.'
            ]);
        }

        // 2. Validasi Input
        $rules = [
            'name' => 'required|min_length[3]|max_length[50]|is_unique[roles.name]',
            'description' => 'max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            // 3. Insert Data
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description')
            ];

            $this->roleModel->insert($data);

            // 4. Return Success Response
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peran baru berhasil ditambahkan',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Gagal menyimpan peran: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat menyimpan peran.'
            ]);
        }
    }

    public function deleteRole(int $roleId)
    {
        // Cek Hak Akses
        if (!has_permission('role-manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak untuk menghapus peran.']);
        }

        // Peran Admin (ID 1) tidak boleh dihapus
        if ($roleId == 1) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Peran Administrator tidak dapat dihapus.']);
        }

        try {
            // Cek: Apakah ada pengguna yang masih menggunakan peran ini?
            $userCount = $this->userModel->where('role_id', $roleId)->countAllResults();
            
            if ($userCount > 0) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => "Tidak dapat menghapus peran. Terdapat {$userCount} pengguna yang masih menggunakan peran ini."]);
            }

            // Hapus Peran: Relasi di role_permissions akan otomatis terhapus (CASCADE)
            $this->roleModel->delete($roleId);

            return $this->response->setJSON(['success' => true, 'message' => 'Peran berhasil dihapus.']);
        } catch (\Exception $e) {
            log_message('error', 'Gagal menghapus peran: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menghapus peran karena kesalahan server.']);
        }
    }
}