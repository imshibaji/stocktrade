<?php

namespace App\Models;

use CodeIgniter\Model;

class WatchlistBucketModel extends Model
{
    protected $table = 'watchlist_buckets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'name'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    public function getUserBuckets(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('name', 'ASC')->findAll();
    }
}
