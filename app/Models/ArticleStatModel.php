<?php
// app/Models/ArticleStatModel.php

namespace App\Models;

use CodeIgniter\Model;

class ArticleStatModel extends Model
{
    protected $table            = 'article_stats';
    protected $primaryKey       = 'article_id'; // Kunci utama adalah article_id (relasi 1:1)
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['article_id', 'views', 'likes', 'dislikes'];

    protected $useTimestamps = false; 
}