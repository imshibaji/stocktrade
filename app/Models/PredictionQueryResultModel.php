<?php

namespace App\Models;

use CodeIgniter\Model;

class PredictionQueryResultModel extends Model
{
    protected $table = 'prediction_query_results';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'query_id',
        'stock_id',
        'predicted_price',
        'predicted_change_pct',
        'signal',
        'confidence_score',
        'method',
        'horizon_days',
        'actual_price',
        'actual_change_pct',
        'outcome',
        'forecast_date',
    ];

    public function getByQueryId(int $queryId): array
    {
        return $this->db->table('prediction_query_results')
            ->select('prediction_query_results.*, stocks.symbol as stock_symbol, stocks.name as stock_name')
            ->join('stocks', 'stocks.id = prediction_query_results.stock_id', 'left')
            ->where('prediction_query_results.query_id', $queryId)
            ->orderBy('prediction_query_results.generated_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getByStockId(int $stockId): array
    {
        return $this->db->table('prediction_query_results')
            ->select('prediction_query_results.*, stocks.symbol as stock_symbol, stocks.name as stock_name')
            ->join('stocks', 'stocks.id = prediction_query_results.stock_id', 'left')
            ->where('prediction_query_results.stock_id', $stockId)
            ->orderBy('prediction_query_results.generated_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getPendingActual(): array
    {
        return $this->where('outcome', 'pending')
            ->where('forecast_date <', date('Y-m-d'))
            ->findAll();
    }

    public function updateOutcome(int $id, ?float $actualPrice, ?float $actualChangePct): bool
    {
        $outcome = 'pending';
        if ($actualPrice !== null && $actualChangePct !== null) {
            $outcome = 'hit';
        } elseif ($actualPrice !== null || $actualChangePct !== null) {
            $outcome = 'miss';
        }

        $data = [
            'actual_price' => $actualPrice,
            'actual_change_pct' => $actualChangePct,
            'outcome' => $outcome,
        ];

        return $this->update($id, $data);
    }
}