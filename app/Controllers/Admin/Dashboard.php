<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $articleModel;
    protected $userModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Hitung Status Artikel
        $publishedCount = $this->articleModel->where('status', 'published')->countAllResults();
        $pendingCount = $this->articleModel->where('status', 'pending')->countAllResults();

        // Hitung Pengunjung Berlangganan
        $subscriberCount = $this->userModel->where('is_subscribed', 1)->countAllResults();

        $data = [
            'title' => 'Dashboard Seputar Dunia',
            'stats' => [
                'published' => $publishedCount,
                'pending' => $pendingCount,
                'subscribers' => $subscriberCount,
            ]
        ];
        
        // Panggilan View sudah benar
        return view('admin/dashboard/index', $data); 
    }
}