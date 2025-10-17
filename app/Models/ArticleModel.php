<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table            = 'articles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 
        'content', 'image_url', 'source_api_id', 'status', 
        'is_featured', 'published_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|max_length[255]',
        'content' => 'required',
        'user_id' => 'required|integer',
        'category_id' => 'required|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    
    // Fungsi untuk mendapatkan semua artikel dengan nama penulis dan kategori
    public function getArticlesWithDetails()
    {
        // Perbaikan: Tambahkan 'articles.' untuk menghilangkan ambiguitas
        $query = $this->select('articles.*, users.username as author_name, categories.name as category_name, article_stats.views')
                    ->join('users', 'users.id = articles.user_id', 'left')
                    ->join('categories', 'categories.id = articles.category_id', 'left')
                    ->join('article_stats', 'article_stats.article_id = articles.id', 'left');
        
        // Kembalikan objek query jika Anda ingin menambahkan where() di Controller
        return $query;
    }

    // Fungsi untuk mendapatkan tags sebagai string untuk form edit
    public function getTagsAsString(int $articleId): string
    {
        $tags = $this->db->table('article_tags')
                        ->select('tags.name')
                        ->join('tags', 'tags.id = article_tags.tag_id')
                        ->where('article_tags.article_id', $articleId)
                        ->get()
                        ->getResultArray();
        
        // Mengubah array tags menjadi string yang dipisahkan koma
        return implode(', ', array_column($tags, 'name'));
    }

    /**
     * Mengambil artikel dengan views tertinggi (untuk featured).
     */
    public function getFeaturedByViews(int $limit = 1)
    {
        return $this->select('articles.*, users.username as author_name, categories.name as category_name, article_stats.views')
                    ->join('users', 'users.id = articles.user_id', 'left')
                    ->join('categories', 'categories.id = articles.category_id', 'left')
                    ->join('article_stats', 'article_stats.article_id = articles.id', 'left')
                    
                    // PERBAIKAN: Kualifikasi kolom status
                    ->where('articles.status', 'published') 
                    
                    ->orderBy('article_stats.views', 'DESC')
                    ->findAll($limit);
    }
}