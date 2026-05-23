<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
// Tambahkan semua Model yang dibutuhkan
use App\Models\ArticleModel; 
use App\Models\CategoryModel;
use App\Models\TagModel;      // Diperlukan untuk method create()
use App\Models\ArticleTagModel; // Diperlukan untuk method create()
use CodeIgniter\Database\ConnectionInterface; // Diperlukan untuk dependency injection $db

class ArticleController extends BaseController
{
    // Deklarasi Properti
    protected $articleModel;
    protected $categoryModel;
    protected $db; // Deklarasikan properti database

    public function __construct()
    {
        // Inisialisasi Models dan Database
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
        $this->db = \Config\Database::connect(); // Ambil instance database
        
        // Memuat helper 'acl' dan 'url' secara eksplisit
        helper(['acl', 'url', 'form']); 
    }

    /**
     * Menampilkan daftar semua artikel (dengan otorisasi Editor/Admin)
     */
    public function index()
    {
        // --- Perbaikan Error: has_permission ---
        // 'has_permission' sekarang dikenali karena helper(['acl']) dimuat di __construct

        // Otorisasi: Hanya Editor dan Admin yang bisa melihat semua artikel
        if (!has_permission('article-review') && !has_permission('article-edit-all')) {
            // Penulis hanya melihat artikelnya sendiri
            // --- Perbaikan Error: Undefined property '$articleModel' (index) ---
            $articles = $this->articleModel->where('user_id', session()->get('user_id'))->getArticlesWithDetails();
            $viewTitle = 'Artikel Saya';
        } else {
            // Admin/Editor melihat semua artikel
            $articles = $this->articleModel->getArticlesWithDetails();
            $viewTitle = 'Manajemen Semua Artikel';
        }

        $data = [
            'title' => $viewTitle,
            'articles' => $articles,
        ];
        return view('admin/articles/index', $data);
    }
    
    /**
     * Menampilkan form untuk membuat artikel baru (Create)
     */
    public function new()
    {
        if (!has_permission('article-create')) {
            return redirect()->to(base_url('admin'))->with('error', 'Anda tidak memiliki hak akses untuk membuat artikel.');
        }

        $data = [
            'title' => 'Buat Artikel Baru',
            'categories' => $this->categoryModel->findAll(),
            'validation' => \Config\Services::validation(),
        ];
        return view('admin/articles/form', $data);
    }


    /**
     * Menyimpan data artikel baru dan relasi tags. (Metode POST)
     */
    public function create() // Menggunakan create() untuk metode POST
    {
        // Otorisasi: Cek hak akses untuk membuat artikel
        if (!has_permission('article-create')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        // 1. Validasi Input
        $rules = [
            'title' => 'required|max_length[255]',
            'category_id' => 'required|integer',
            'content' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Ambil data
        $data = $this->request->getPost(); // <-- HARUS MENGGUNAKAN getPost()
        $tagsInput = $data['tags'] ?? '';
        $status = ($data['action'] === 'pending') ? 'pending' : 'draft';

        // 2. Transaksi Database
        $this->db->transBegin(); // --- Perbaikan Error: Undefined property '$db' ---

        try {
            // A. Data Artikel
            $articleData = [
                'user_id' => session()->get('user_id'),
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'slug' => url_title($data['title'], '-', true),
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'image_url' => $data['image_url'],
                'status' => $status,
                'is_featured' => 0,
                'published_at' => ($status === 'published') ? date('Y-m-d H:i:s') : null,
            ];

            // Simpan Artikel
            $this->articleModel->insert($articleData); // --- Perbaikan Error: Undefined property '$articleModel' (create) ---
            $articleId = $this->articleModel->getInsertID();

            // B. Data Tags (Relasi Many-to-Many)
            if (!empty($tagsInput) && $articleId) {
                // --- Perbaikan Error: Undefined type TagModel & ArticleTagModel ---
                $tagModel = new TagModel(); 
                $articleTagModel = new ArticleTagModel();
                $tagNames = array_map('trim', explode(',', $tagsInput));

                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;
                    
                    $existingTag = $tagModel->where('name', $tagName)->first();

                    if ($existingTag) {
                        $tagId = $existingTag['id'];
                    } else {
                        $tagModel->insert(['name' => $tagName]);
                        $tagId = $tagModel->getInsertID();
                    }

                    // Hubungkan Tag ke Artikel
                    $articleTagModel->insert([
                        'article_id' => $articleId,
                        'tag_id' => $tagId,
                    ]);
                }
            }

            // 3. Commit Transaksi
            $this->db->transCommit();
            
            $message = "Artikel berhasil disimpan sebagai " . strtoupper($status) . ".";
            
            return $this->response->setJSON(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            // 4. Rollback jika terjadi kesalahan
            $this->db->transRollback();
            log_message('error', 'Gagal menyimpan artikel: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan artikel. Terjadi kesalahan database.'
            ]);
        }
    }

    public function edit($id = null)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }
        
        // Otorisasi: Admin/Editor bisa edit semua, Penulis hanya edit miliknya sendiri
        $canEdit = has_permission('article-edit-all') || 
                (has_permission('article-edit-own') && $article['user_id'] == session()->get('user_id'));

        if (!$canEdit) {
            return redirect()->to(base_url('admin/articles'))->with('error', 'Anda tidak memiliki hak akses untuk mengedit artikel ini.');
        }
        
        // Ambil tags artikel saat ini (untuk mengisi input tags)
        $articleTags = $this->articleModel->getTagsAsString($id); // Membutuhkan fungsi getTagsAsString di ArticleModel

        $data = [
            'title' => 'Edit Artikel: ' . $article['title'],
            'article' => $article, // Data artikel yang akan di-edit
            'categories' => $this->categoryModel->findAll(),
            'articleTags' => $articleTags, // Tags dalam bentuk string (tag1, tag2)
            'validation' => \Config\Services::validation(),
        ];
        return view('admin/articles/form', $data);
    }

    /**
     * Memperbarui data artikel yang sudah ada.
     */
    public function update($id = null)
    {
        $article = $this->articleModel->find($id);
        if (!$article) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Artikel tidak ditemukan.']);
        }

        // Otorisasi: Perlu dicek lagi saat update
        $canEdit = has_permission('article-edit-all') || 
                (has_permission('article-edit-own') && $article['user_id'] == session()->get('user_id'));

        if (!$canEdit) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak untuk memperbarui artikel ini.']);
        }
        
        // 1. Validasi Input (Sama seperti create)
        $rules = [
            'title' => 'required|max_length[255]',
            'category_id' => 'required|integer',
            'content' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Gunakan getPost() karena AJAX sekarang akan mengirim FormData native dengan _method=PUT
        $input = $this->request->getPost();

        // Lanjutkan dengan input baru
        $tagsInput = $input['tags'] ?? '';
        $status = ($input['action'] === 'pending') ? 'pending' : (($input['action'] === 'published') ? 'published' : 'draft');


        // 2. Transaksi Database
        $this->db->transBegin();

        try {
            // A. Data Artikel
            $articleData = [
                'id' => $id, // Penting untuk update
                
                // --- PERBAIKAN DI SINI: Ganti $data['field'] menjadi $input['field'] ---
                'category_id' => $input['category_id'],
                'title' => $input['title'],
                'slug' => url_title($input['title'], '-', true),
                'excerpt' => $input['excerpt'],
                'content' => $input['content'],
                'image_url' => $input['image_url'],
                // --- END PERBAIKAN ---

                'status' => $status,
                'published_at' => ($status === 'published' && !$article['published_at']) ? date('Y-m-d H:i:s') : $article['published_at'],
            ];

            // Simpan Perubahan Artikel
            $this->articleModel->save($articleData);

            // B. Update Tags: Hapus semua tags lama, lalu masukkan tags baru
            $tagModel = new \App\Models\TagModel();
            $articleTagModel = new \App\Models\ArticleTagModel();
            
            // Hapus relasi lama
            $articleTagModel->where('article_id', $id)->delete();

            if (!empty($tagsInput)) {
                $tagNames = array_map('trim', explode(',', $tagsInput));

                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;
                    
                    $existingTag = $tagModel->where('name', $tagName)->first();

                    if ($existingTag) {
                        $tagId = $existingTag['id'];
                    } else {
                        $tagModel->insert(['name' => $tagName]);
                        $tagId = $tagModel->getInsertID();
                    }

                    $articleTagModel->insert([
                        'article_id' => $id,
                        'tag_id' => $tagId,
                    ]);
                }
            }

            $this->db->transCommit();
            
            $message = "Artikel berhasil diperbarui dan disimpan sebagai " . strtoupper($status) . ".";
            
            return $this->response->setJSON(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Gagal memperbarui artikel: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui artikel. Terjadi kesalahan database.'
            ]);
        }
    }

    /**
    * Mempublikasikan atau menyetujui artikel (Digunakan oleh Editor/Admin).
    */
    public function publish($id = null)
    {
        // Otorisasi: Hanya yang memiliki hak 'article-review' yang bisa menyetujui
        if (!has_permission('article-review')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki hak untuk menyetujui artikel.']);
        }

        $article = $this->articleModel->find($id);
        if (!$article) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Artikel tidak ditemukan.']);
        }

        try {
            $updateData = [
                'id' => $id,
                'status' => 'published',
                // Set published_at jika belum pernah dipublikasikan
                'published_at' => $article['published_at'] ?? date('Y-m-d H:i:s'),
            ];

            $this->articleModel->save($updateData);

            return $this->response->setJSON(['success' => true, 'message' => 'Artikel berhasil dipublikasikan!']);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal mempublikasikan artikel: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus artikel.
     */
    public function delete($id = null)
    {
        // Otorisasi: Hanya yang memiliki hak 'article-edit-all' (Admin/Editor) yang bisa menghapus
        if (!has_permission('article-edit-all')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki hak untuk menghapus artikel.']);
        }

        $article = $this->articleModel->find($id);
        if (!$article) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Artikel tidak ditemukan.']);
        }
        
        // Resource route DELETE TIDAK MEMILIKI CSRF default, kita harus menangani data secara RAW jika menggunakan form.
        
        try {
            // Hapus artikel. Karena relasi dibuat CASCADE, tags dan stats akan terhapus otomatis.
            $this->articleModel->delete($id);

            return $this->response->setJSON(['success' => true, 'message' => 'Artikel berhasil dihapus.']);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menghapus artikel: ' . $e->getMessage()]);
        }
    }
}