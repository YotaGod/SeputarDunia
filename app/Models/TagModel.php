<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name'];
    protected $useTimestamps    = false; 
    protected $validationRules    = [
        'name' => 'required|max_length[100]|is_unique[tags.name,id,{id}]',
    ];
}