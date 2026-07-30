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
    protected $updatedField = '';

    public function getPredictionsForStock(int $stockId, int $days = 30)
    {
        return $this->where('stock_id', $stockId)
            ->where('predicted_date >=', date('Y-m-d'))
            ->orderBy('predicted_date', 'ASC')
            ->limit($days)
            ->findAll();
    }

    public function getPredictionsForStocks(array $stockIds, int $days = 30): array
    {
        if (empty($stockIds)) return [];

        return $this->whereIn('stock_id', $stockIds)
            ->where('predicted_date >=', date('Y-m-d'))
            ->orderBy('stock_id, predicted_date', 'ASC')
            ->findAll();
    }
}
