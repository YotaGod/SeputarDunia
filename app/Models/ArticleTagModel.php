<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleTagModel extends Model
{
    protected $table            = 'article_tags';
    protected $allowedFields    = ['article_id', 'tag_id'];
    protected $useTimestamps    = false; 
    // Tidak ada primaryKey karena ini adalah tabel pivot komposit
}