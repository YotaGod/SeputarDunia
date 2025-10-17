<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ArticleModel;

class Category extends BaseController
{
    protected $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    /**
     * Menampilkan daftar artikel berdasarkan slug kategori.
     * Dapat dipanggil via full-page load atau AJAX.
     */
    public function index(?string $categorySlug = null)
    {
        $isAjax = $this->request->isAJAX();

        if (empty($categorySlug)) {
            // Jika dipanggil tanpa slug, redirect ke home
            return redirect()->to(base_url());
        }

        $categoryData = $this->categoryModel->where('slug', $categorySlug)->first();

        if (!$categoryData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        // Fetch articles
        $articles = $this->articleModel
                         ->select('articles.*, users.username, categories.name AS category_name')
                         ->join('users', 'users.id = articles.user_id')
                         ->join('categories', 'categories.id = articles.category_id')
                         ->where('articles.category_id', $categoryData['id'])
                         ->where('articles.status', 'published')
                         ->orderBy('articles.published_at', 'DESC')
                         ->findAll();

        $pageData = [
            'title'    => 'Berita Kategori: ' . $categoryData['name'],
            'currentCategory' => $categoryData,
            'articles' => $articles,
        ];
        
        // Jika request datang dari AJAX, kembalikan hanya konten parsial
        if ($isAjax) {
            // Memanggil partial view yang hanya berisi card artikel
            return view('frontend/category/_article_list', array_merge($this->data, $pageData));
        }

        // Jika request normal (full page load)
        return view('frontend/category/index', array_merge($this->data, $pageData));
    }
}
