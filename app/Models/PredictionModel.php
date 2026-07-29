<?php

namespace App\Models;

use CodeIgniter\Model;

class PredictionModel extends Model
{
    protected $table = 'predictions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['stock_id', 'predicted_date', 'predicted_price', 'confidence_score', 'method'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    public function getPredictionsForStock(int $stockId, int $days = 30)
    {
        return $this->where('stock_id', $stockId)
            ->where('predicted_date >=', date('Y-m-d'))
            ->orderBy('predicted_date', 'ASC')
            ->limit($days)
            ->findAll();
    }
}
