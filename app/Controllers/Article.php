<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ArticleModel;
use App\Models\ArticleStatModel;
use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Article extends BaseController
{
    protected $articleModel;
    protected $articleStatModel;
    protected $commentModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->articleStatModel = new ArticleStatModel();
        $this->commentModel = new CommentModel();
        
        // Memuat helper 'acl' (untuk is_subscriber())
        // Ini adalah cara yang benar, dan seharusnya sudah menyelesaikan error tersebut.
        helper('acl'); 
    }

    public function index()
    {
        return redirect()->to(base_url());
    }
    
    public function detail(string $slug) // Sekarang $slug akan berisi seluruh string panjang
    {
        // 1. Ambil data artikel dengan JOIN ke user dan category
        $article = $this->articleModel
            ->select('articles.*, users.username, categories.slug AS category_slug, categories.name AS category_name')
            ->join('users', 'users.id = articles.user_id')
            ->join('categories', 'categories.id = articles.category_id')
            ->where('articles.slug', $slug)
            ->where('articles.status', 'published')
            ->first();

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $publishedTime = $article['published_at'];

        $articleId = $article['id'];

        $this->logArticleView($articleId);

        $stats = $this->articleStatModel->find($articleId) ?? ['views' => 0, 'likes' => 0, 'dislikes' => 0];

        $comments = $this->commentModel->where('article_id', $articleId)->where('status', 'approved')->findAll(); 

        // Fungsi helper harus dipanggil tanpa namespace:
        $data = [
            'article' => $article,
            'stats' => $stats,
            'comments' => $comments,
            'canComment' => is_subscriber(), // DIPANGGIL TANPA NAMESPACE
            'isSubscriber' => is_subscriber(), // DIPANGGIL TANPA NAMESPACE
            'title' => $article['title'],
        ];

        return view('frontend/article/detail', array_merge($this->data, $data));
    }

    protected function logArticleView(int $articleId)
    {
        $stats = $this->articleStatModel->find($articleId);
        
        if ($stats) {
            $this->articleStatModel->update($articleId, ['views' => $stats['views'] + 1]);
        } else {
            $this->articleStatModel->insert(['article_id' => $articleId, 'views' => 1, 'likes' => 0, 'dislikes' => 0]);
        }
    }


    public function submitComment()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Anda harus login untuk berkomentar.']);
        }
        
        // Cek hak akses: DIPANGGIL TANPA NAMESPACE
        if (!is_subscriber()) { 
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Hanya Pengunjung Berlangganan yang diizinkan berkomentar.']);
        }
        
        $rules = [
            'article_id' => 'required|integer',
            'content' => 'required|min_length[5]|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'article_id' => $this->request->getPost('article_id'),
            'user_id' => session()->get('user_id'),
            'content' => $this->request->getPost('content'),
            'status' => 'pending', 
        ];

        $this->commentModel->insert($data);

        return $this->response->setJSON(['success' => true, 'message' => 'Komentar Anda berhasil dikirim dan akan muncul setelah disetujui moderator.']);
    }

    public function updateStat()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Anda harus login untuk memberi rating.']);
        }
        
        $articleId = $this->request->getPost('article_id');
        $action = $this->request->getPost('action');

        if (!in_array($action, ['like', 'dislike']) || !$articleId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Permintaan tidak valid.']);
        }

        $stats = $this->articleStatModel->find($articleId) ?? ['likes' => 0, 'dislikes' => 0];
        
        $updateData = [];
        if ($action === 'like') {
            $updateData['likes'] = $stats['likes'] + 1;
        } else {
            $updateData['dislikes'] = $stats['dislikes'] + 1;
        }

        $this->articleStatModel->save(['article_id' => $articleId] + $updateData);

        return $this->response->setJSON(['success' => true, 'message' => 'Rating berhasil dicatat.']);
    }
}
