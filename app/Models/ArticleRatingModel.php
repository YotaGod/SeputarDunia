<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleRatingModel extends Model
{
    protected $table            = 'article_ratings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'article_id', 'action', 'created_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Tidak pakai updated_at

    /**
     * Memeriksa apakah user sudah pernah rating (like/dislike) artikel ini.
     */
    public function hasUserRated(int $userId, int $articleId)
    {
        return $this->where('user_id', $userId)
                    ->where('article_id', $articleId)
                    ->first();
    }
}
