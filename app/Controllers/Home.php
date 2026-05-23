<?php

namespace App\Controllers;

use App\Libraries\NewsApiService;
use App\Models\ArticleModel; // Digunakan di Tahap 4

class Home extends BaseController
{
    // 1. Deklarasi Properti
    protected $newsApi;
    protected $articleModel;

    // 2. Inisialisasi Properti di Konstruktor
    public function __construct()
    {
        // Pastikan kelas ini ada: app/Libraries/NewsApiService.php
        $this->newsApi = new NewsApiService();
        
        // Pastikan kelas ini ada: app/Models/ArticleModel.php
        // Kita juga inisialisasi Model lokal di sini
        $this->articleModel = new ArticleModel(); 
    }

    public function index()
    {
        // 1. Ambil Berita dari News API (tetap sama)
        $apiArticles = $this->newsApi->fetchEverything('teknologi', 5);
        $latestNews = ($apiArticles['status'] === 'ok' && isset($apiArticles['articles'])) ? $apiArticles['articles'] : [];
        
        // 2. Ambil Artikel Unggulan (Views Tertinggi)
        $featuredArticles = $this->articleModel->getFeaturedByViews(1); // Ambil 1 artikel
        
        // 3. Ambil Artikel Lokal Terbaru
        $localArticles = $this->articleModel
            // PERBAIKAN: Kualifikasi status
            ->where('articles.status', 'published')
            
            ->whereNotIn('articles.id', array_column($featuredArticles, 'id')) 
            ->orderBy('published_at', 'DESC')
            ->getArticlesWithDetails()
            ->findAll(); // Jangan lupa findAll() jika Anda tidak membatasi limit di Model

        
        $data = [
            'title'             => 'Seputar Dunia - Berita Terkini',
            'latestNews'        => $latestNews,
            'featuredArticles'  => $featuredArticles,
            'localArticles'     => $localArticles,
            'newsApiError'      => ($apiArticles['status'] !== 'ok') ? $apiArticles['message'] : null,
        ];

        // 4. Return View dengan data yang lengkap
        return view('home/index', array_merge($this->data, $data));
    }
}