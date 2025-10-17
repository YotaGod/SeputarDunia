<?php
// app/Models/SubscriptionModel.php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table            = 'subscriptions';
    protected $primaryKey       = 'user_id'; 
    protected $allowedFields    = ['user_id', 'start_date', 'end_date', 'status', 'payment_method'];
    protected $useTimestamps    = false; 
}